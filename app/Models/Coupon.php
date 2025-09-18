<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $table = 'coupons';
    protected $primaryKey = 'coupon_id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'description',
        'discount_amount',   // số tiền giảm (đ)
        'discount_percent',  // % giảm (0..100)
        'valid_from',
        'valid_to', // DATE (yyyy-mm-dd)
        'usage_limit',
        'used_count',
    ];

    // Còn hạn & chưa vượt giới hạn sử dụng
    public function scopeActive($q)
    {
        $today = Carbon::today()->toDateString();
        return $q->where(function ($x) use ($today) {
            $x->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
        })
            ->where(function ($x) use ($today) {
                $x->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
            })
            ->where(function ($x) {
                // nếu có usage_limit thì used_count < usage_limit
                $x->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    // Chuỗi hiển thị giá trị giảm
    public function getDisplayOffAttribute(): string
    {
        if ($this->discount_percent) {
            return '-' . (int)$this->discount_percent . '%';
        }
        if ($this->discount_amount) {
            return '-' . number_format((float)$this->discount_amount, 0, ',', '.') . 'đ';
        }
        return 'Ưu đãi';
    }

    // Số lượt còn lại (nếu có giới hạn)
    public function getRemainingAttribute(): ?int
    {
        if (is_null($this->usage_limit)) return null;
        return max(0, (int)$this->usage_limit - (int)$this->used_count);
    }
}
