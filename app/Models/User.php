<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'address',
        'role_id',
        'is_active',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime', // Chuyển last_activity thành Carbon
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class, 'user_id', 'user_id');
    // }

    public function hasPermission($permission)
    {
        return $this->role && $this->role->permissions()->where('permission_name', $permission)->exists();
    }

    public function hasRole($roles)
    {
        if (is_string($roles)) {
            $roles = explode('|', $roles);
        }
        return $this->role && in_array($this->role->role_name, (array)$roles);
    }

    public function isOnline()
    {
        if (!$this->last_activity) {
            return false;
        }
        return now()->diffInMinutes($this->last_activity) < 5;
    }
}
