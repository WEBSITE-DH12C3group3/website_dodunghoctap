<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    // Nếu cột khác tên: permission_name / is_allowed → chỉnh $fillable cho đúng
    protected $fillable = ['role_id', 'permission', 'allowed'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
