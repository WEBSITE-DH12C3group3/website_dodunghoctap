<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return view('store.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string', 'max:255'],
        ], [], [
            'full_name' => 'Họ và tên',
            'phone'     => 'Số điện thoại',
            'address'   => 'Địa chỉ',
        ]);

        $user = $request->user();
        $user->full_name = trim($data['full_name']);
        $user->phone     = $data['phone'] ?? null;
        $user->address   = $data['address'] ?? null;
        $user->save();

        return back()->with('status', 'Cập nhật thông tin thành công!');
    }
}
