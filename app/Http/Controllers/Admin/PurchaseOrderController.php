<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Exports\PurchaseOrdersExport;
use Maatwebsite\Excel\Facades\Excel; //
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
  public function index(Request $request)
{
    Log::info('PurchaseOrderController@index called, fetching purchase orders');

    try {
        $query = PurchaseOrder::with('user', 'supplier');

        // 🔍 Tìm kiếm
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('code', 'LIKE', "%{$s}%")
                  ->orWhereHas('supplier', fn($qq) => $qq->where('supplier_name', 'LIKE', "%{$s}%"))
                  ->orWhereHas('user', fn($qq) => $qq->where('full_name', 'LIKE', "%{$s}%"));
            });
            Log::info("Purchase order search: ".$s);
        }

        // 📅 Lọc theo ngày nhập
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        // 💰 Lọc theo tổng tiền
        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', (float) $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', (float) $request->amount_max);
        }

        // 👤 Lọc theo người nhập
        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }

        // 🏢 Lọc theo nhà cung cấp
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchaseOrders = $query->orderBy('order_date', 'desc')
                                ->paginate(10)
                                ->withQueryString();

        // Lấy dữ liệu dropdown cho form lọc
        $users = \App\Models\User::select('user_id','full_name')->get();
        $suppliers = \App\Models\Supplier::select('supplier_id','supplier_name')->get();

        Log::info('Purchase orders fetched (page): '.$purchaseOrders->count().', total: '.$purchaseOrders->total());

        return view('admin.purchase_orders.index', compact('purchaseOrders','users','suppliers'));
    } catch (\Exception $e) {
        Log::error('Error in PurchaseOrderController@index: ' . $e->getMessage());
        return redirect()->route('admin.purchase_orders')->with('error', 'Lỗi khi tải danh sách phiếu nhập kho: ' . $e->getMessage());
    }
}


    public function create()
    {
        Log::info('PurchaseOrderController@create called');
        $products = Product::select('product_id', 'product_name', 'price')->get();
        $suppliers = Supplier::all();
        return view('admin.purchase_orders.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        Log::info('PurchaseOrderController@store called with data:', $request->all());

        $validated = $request->validate([
            'order_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ], [
            'order_date.required' => 'Vui lòng chọn ngày nhập.',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm.',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm cho mục #{index}.',
            'items.*.quantity.required' => 'Vui lòng nhập số lượng cho sản phẩm #{index}.',
            'items.*.quantity.min' => 'Số lượng sản phẩm #{index} phải lớn hơn hoặc bằng 1.',
            'items.*.price.required' => 'Vui lòng nhập giá nhập cho sản phẩm #{index}.',
            'items.*.price.min' => 'Giá nhập sản phẩm #{index} phải lớn hơn hoặc bằng 0.',
        ]);

        DB::beginTransaction();
        try {
            $total_amount = 0;
            foreach ($validated['items'] as $item) {
                $total_amount += $item['quantity'] * $item['price'];
            }

            $purchaseOrder = PurchaseOrder::create([
                'order_date' => $validated['order_date'],
                'total_amount' => $total_amount,
                'supplier_id' => $validated['supplier_id'],
                'created_by' => Auth::id(),
            ]);

            $code = 'PN-' . date('Ym', strtotime($validated['order_date'])) . '-' . str_pad($purchaseOrder->purchase_order_id, 3, '0', STR_PAD_LEFT);
            $purchaseOrder->update(['code' => $code]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->purchase_order_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::findOrFail($item['product_id']);
                $product->stock_quantity += $item['quantity'];
                $product->save();
            }

            DB::commit();
            Log::info('Purchase order created successfully: ' . $purchaseOrder->code);
            return redirect()->route('admin.purchase_orders')->with('ok', 'Tạo phiếu nhập kho thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing purchase order: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi tạo phiếu nhập kho: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with('user', 'supplier', 'items.product')->findOrFail($id);
        return view('admin.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);
        $products = Product::select('product_id', 'product_name', 'price')->get();
        $suppliers = Supplier::all();
        return view('admin.purchase_orders.edit', compact('purchaseOrder', 'products', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $validated = $request->validate([
            'order_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ], [
            'order_date.required' => 'Vui lòng chọn ngày nhập.',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm.',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm cho mục #{index}.',
            'items.*.quantity.required' => 'Vui lòng nhập số lượng cho sản phẩm #{index}.',
            'items.*.quantity.min' => 'Số lượng sản phẩm #{index} phải lớn hơn hoặc bằng 1.',
            'items.*.price.required' => 'Vui lòng nhập giá nhập cho sản phẩm #{index}.',
            'items.*.price.min' => 'Giá nhập sản phẩm #{index} phải lớn hơn hoặc bằng 0.',
        ]);

        DB::beginTransaction();
        try {
            foreach ($purchaseOrder->items as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->stock_quantity -= $item->quantity;
                $product->save();
            }

            PurchaseOrderItem::where('purchase_order_id', $id)->delete();

            $total_amount = 0;
            foreach ($validated['items'] as $item) {
                $total_amount += $item['quantity'] * $item['price'];
            }

            $purchaseOrder->update([
                'order_date' => $validated['order_date'],
                'total_amount' => $total_amount,
                'supplier_id' => $validated['supplier_id'],
            ]);

            $code = 'PN-' . date('Ym', strtotime($validated['order_date'])) . '-' . str_pad($purchaseOrder->purchase_order_id, 3, '0', STR_PAD_LEFT);
            $purchaseOrder->update(['code' => $code]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->purchase_order_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::findOrFail($item['product_id']);
                $product->stock_quantity += $item['quantity'];
                $product->save();
            }

            DB::commit();
            Log::info('Purchase order updated successfully: ' . $purchaseOrder->code);
            return redirect()->route('admin.purchase_orders')->with('ok', 'Cập nhật phiếu nhập kho thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating purchase order: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi cập nhật phiếu nhập kho: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($purchaseOrder->items as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->stock_quantity -= $item->quantity;
                $product->save();
            }

            PurchaseOrderItem::where('purchase_order_id', $id)->delete();
            $purchaseOrder->delete();

            DB::commit();
            Log::info('Purchase order deleted successfully: ID ' . $id);
            return redirect()->route('admin.purchase_orders')->with('ok', 'Xóa phiếu nhập kho thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting purchase order: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa phiếu nhập kho: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
{
    $format = $request->query('format');
    $query = PurchaseOrder::query()
        ->with(['user', 'supplier'])
        ->when($request->search, fn($q) => $q->where('code', 'like', "%{$request->search}%")
            ->orWhereHas('user', fn($q) => $q->where('full_name', 'like', "%{$request->search}%"))
            ->orWhereHas('supplier', fn($q) => $q->where('supplier_name', 'like', "%{$request->search}%")))
        ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
        ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
        ->when($request->amount_min, fn($q) => $q->where('total_amount', '>=', $request->amount_min))
        ->when($request->amount_max, fn($q) => $q->where('total_amount', '<=', $request->amount_max))
        ->when($request->user_id, fn($q) => $q->where('created_by', $request->user_id))
        ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id));

    if ($format === 'excel') {
        return Excel::download(new PurchaseOrdersExport($query->get()), 'purchase_orders.xlsx');
    } elseif ($format === 'pdf') {
        $purchaseOrders = $query->get();
        $pdf = Pdf::loadView('admin.purchase_orders.pdf', compact('purchaseOrders'));
        return $pdf->download('purchase_orders.pdf');
    }

    return redirect()->back()->with('error', 'Invalid export format');
}
}