<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $r)
    {
        $q = Permission::query();
        if ($s = $r->get('search')) {
            $q->where('permission_name', 'like', "%$s%");
        }
        $permissions = $q->orderBy('permission_id')->paginate(30);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'permission_name' => 'required|string|max:100|unique:permissions,permission_name',
            'description' => 'nullable|string'
        ]);
        Permission::create($data);
        return back()->with('ok', 'Đã tạo permission');
    }

    public function update(Request $r, $id)
    {
        $perm = Permission::findOrFail($id);
        $data = $r->validate([
            'permission_name' => "required|string|max:100|unique:permissions,permission_name,{$perm->permission_id},permission_id",
            'description' => 'nullable|string'
        ]);
        $perm->update($data);
        return back()->with('ok', 'Đã cập nhật');
    }

    public function destroy($id)
    {
        $perm = Permission::findOrFail($id);
        $perm->delete();
        return back()->with('ok', 'Đã xóa');
    }
}
