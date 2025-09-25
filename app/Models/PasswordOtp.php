<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PasswordOtp extends Model
{
    protected $table = 'password_otps';
    protected $fillable = ['user_id','code','expires_at','used_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function isExpired(): bool {
        return now()->greaterThan($this->expires_at);
    }

    public function isUsed(): bool {
        return !is_null($this->used_at);
    }
}
