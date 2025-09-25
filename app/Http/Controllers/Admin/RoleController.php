<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $r)
    {
        $q = Role::query()->withCount('users')->with('permissions');
        if ($s = $r->get('search')) {
            $q->where('role_name', 'like', "%$s%");
        }
        $roles = $q->orderBy('role_id')->paginate(20);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name',
            'description' => 'nullable|string'
        ]);
        Role::create($data);
        return redirect()->route('admin.roles')->with('ok', 'Tạo role thành công');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $allPerms = Permission::orderBy('permission_name')->get();
        return view('admin.roles.edit', compact('role', 'allPerms'));
    }

    public function update(Request $r, $id)
    {
        $role = Role::findOrFail($id);
        $data = $r->validate([
            'role_name' => "required|string|max:50|unique:roles,role_name,{$role->role_id},role_id",
            'description' => 'nullable|string'
        ]);
        $role->update($data);
        return redirect()->route('admin.roles.edit', $role->role_id)->with('ok', 'Cập nhật role thành công');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('admin.roles')->with('ok', 'Đã xóa role');
    }

    public function attachPermissions(Request $r, $id)
    {
        $role = Role::findOrFail($id);
        $data = $r->validate(['permission_ids' => 'required|array', 'permission_ids.*' => 'integer']);
        $role->permissions()->syncWithoutDetaching($data['permission_ids']);
        return back()->with('ok', 'Đã gán quyền');
    }

    public function detachPermissions(Request $r, $id)
    {
        $role = Role::findOrFail($id);
        $data = $r->validate(['permission_ids' => 'required|array', 'permission_ids.*' => 'integer']);
        $role->permissions()->detach($data['permission_ids']);
        return back()->with('ok', 'Đã bỏ quyền');
    }
}
