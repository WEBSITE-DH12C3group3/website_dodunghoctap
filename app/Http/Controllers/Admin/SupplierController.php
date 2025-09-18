<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        Log::info('SupplierController@index called, fetching suppliers');
        $suppliers = Supplier::orderBy('supplier_name', 'asc')->get();
        Log::info('Suppliers fetched: ' . $suppliers->count() . ' items');
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        Log::info('SupplierController@create called');
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        Log::info('SupplierController@store called with data:', $request->all());

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
        ], [
            'supplier_name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'supplier_name.max' => 'Tên nhà cung cấp không được vượt quá 255 ký tự.',
            'contact_info.max' => 'Thông tin liên hệ không được vượt quá 255 ký tự.',
        ]);

        DB::beginTransaction();
        try {
            Supplier::create($validated);
            DB::commit();
            Log::info('Supplier created successfully: ' . $validated['supplier_name']);
            return redirect()->route('admin.suppliers')->with('ok', 'Tạo nhà cung cấp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing supplier: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi tạo nhà cung cấp: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit($id)
    {
        Log::info('SupplierController@edit called for supplier_id: ' . $id);
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        Log::info('SupplierController@update called with data:', $request->all());

        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
        ], [
            'supplier_name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'supplier_name.max' => 'Tên nhà cung cấp không được vượt quá 255 ký tự.',
            'contact_info.max' => 'Thông tin liên hệ không được vượt quá 255 ký tự.',
        ]);

        DB::beginTransaction();
        try {
            $supplier->update($validated);
            DB::commit();
            Log::info('Supplier updated successfully: ' . $validated['supplier_name']);
            return redirect()->route('admin.suppliers')->with('ok', 'Cập nhật nhà cung cấp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating supplier: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi cập nhật nhà cung cấp: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($supplier->purchaseOrders()->count() > 0) {
                Log::warning('Attempt to delete supplier with existing purchase orders: supplier_id ' . $id);
                return back()->with('error', 'Không thể xóa nhà cung cấp vì đã có phiếu nhập kho liên quan.');
            }

            $supplier->delete();
            DB::commit();
            Log::info('Supplier deleted successfully: ID ' . $id);
            return redirect()->route('admin.suppliers')->with('ok', 'Xóa nhà cung cấp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting supplier: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa nhà cung cấp: ' . $e->getMessage());
        }
    }
}