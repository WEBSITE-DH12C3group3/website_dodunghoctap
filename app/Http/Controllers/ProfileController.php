<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('user.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name'      => 'nullable|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:255',
        ]);

        $table = 'users';
        $update = [];

        // Tên
        if (Schema::hasColumn($table, 'first_name')) $update['first_name'] = $validated['first_name'] ?? null;
        if (Schema::hasColumn($table, 'last_name'))  $update['last_name']  = $validated['last_name']  ?? null;

        // full_name nếu DB dùng cột này
        if (Schema::hasColumn($table, 'full_name')) {
            $full = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
            if ($full !== '') $update['full_name'] = $full;
        }

        // Các cột khác
        foreach (['phone', 'address'] as $col) {
            if (Schema::hasColumn($table, $col)) $update[$col] = $validated[$col] ?? null;
        }


        if (!empty($update)) {
            // hỗ trợ cả id hoặc user_id
            $pkCol = isset($user->user_id) ? 'user_id' : 'id';
            $pkVal = $user->$pkCol;
            DB::table($table)->where($pkCol, $pkVal)->update($update);
        }

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }
}
