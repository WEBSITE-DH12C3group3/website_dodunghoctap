<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    // Bảng chỉ có created_at; không có updated_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['full_name', 'email', 'phone', 'address', 'password', 'role_id'];
    protected $hidden   = ['password'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function hasPermission(string $permissionName): bool
    {
        $role = $this->role()->with(['permissions' => function ($q) {
            $q->select('permissions.permission_id', 'permissions.permission_name');
        }])->first();

        if (!$role) return false;
        return $role->permissions->contains(fn($p) => $p->permission_name === $permissionName);
    }

    // Hỗ trợ đăng nhập cho dữ liệu cũ còn plaintext (khuyến cáo: chuyển hết sang bcrypt)
    public static function verifyPassword(string $input, string $stored): bool
    {
        return Hash::check($input, $stored) || hash_equals($stored, $input);
    }
}
