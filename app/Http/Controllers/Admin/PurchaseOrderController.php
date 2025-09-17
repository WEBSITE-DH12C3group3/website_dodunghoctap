<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        Log::info('PurchaseOrderController@index called, fetching purchase orders');
        $purchaseOrders = PurchaseOrder::with('user', 'supplier')->orderBy('order_date', 'desc')->get();
        Log::info('Purchase orders fetched: ' . $purchaseOrders->count() . ' items');
        return view('admin.purchase_orders.index', compact('purchaseOrders'));
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
}