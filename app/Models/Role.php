<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

    protected $fillable = ['role_name', 'description', 'created_at'];

    public function permissions()
    {
        // Pivot: role_permissions(role_id, permission_id)
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        );
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
