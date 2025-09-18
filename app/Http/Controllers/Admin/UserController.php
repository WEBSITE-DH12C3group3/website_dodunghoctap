<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        Log::info('UserController@index called, fetching users');
        $users = User::with('role')->orderBy('user_id', 'asc')->get();
        Log::info('Users fetched: ' . $users->count() . ' items');
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        Log::info('UserController@edit called for user_id: ' . $id);
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        Log::info('UserController@update called with data:', $request->all());
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,role_id',
            'is_active' => 'required|boolean',
        ], [
            'role_id.required' => 'Vui lòng chọn vai trò.',
            'role_id.exists' => 'Vai trò không tồn tại.',
            'is_active.required' => 'Vui lòng chọn trạng thái tài khoản.',
        ]);

        DB::beginTransaction();
        try {
            $user->update($validated);
            DB::commit();
            Log::info('User updated successfully: user_id ' . $id);
            return redirect()->route('admin.users')->with('ok', 'Cập nhật người dùng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi cập nhật người dùng: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Log::info('UserController@destroy called for user_id: ' . $id);
        $user = User::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($user->user_id == 104) {
                Log::warning('Attempt to delete main admin user: user_id ' . $id);
                return back()->with('error', 'Không thể xóa tài khoản admin chính!');
            }

            if ($user->hasPermission('manage_purchases') && $user->purchaseOrders()->count() > 0) {
                Log::warning('Attempt to delete user with purchase orders: user_id ' . $id);
                return back()->with('error', 'Không thể xóa người dùng vì có phiếu nhập kho liên quan.');
            }

            $user->delete();
            DB::commit();
            Log::info('User deleted successfully: user_id ' . $id);
            return redirect()->route('admin.users')->with('ok', 'Xóa người dùng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa người dùng: ' . $e->getMessage());
        }
    }
}