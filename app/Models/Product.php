<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';   // nếu DB của bạn dùng "id" thì đổi lại 'id'
    public $timestamps = true;              // nếu không có updated_at thì set const UPDATED_AT = null;

    // Cho phép hiển thị “tên/giá/ảnh” linh hoạt dù tên cột khác nhau
    protected $appends = ['display_name', 'display_price', 'display_sale_price', 'display_image'];

    public function getDisplayNameAttribute()
    {
        return $this->name ?? $this->product_name ?? $this->title ?? 'Sản phẩm';
    }

    public function getDisplayPriceAttribute()
    {
        return $this->price ?? $this->unit_price ?? $this->original_price ?? null;
    }

    public function getDisplaySalePriceAttribute()
    {
        return $this->sale_price ?? $this->discount_price ?? null;
    }

    public function getDisplayImageAttribute()
    {
        foreach (['image', 'thumbnail', 'image_url', 'cover', 'main_image', 'avatar'] as $c) {
            if (!empty($this->{$c})) return $this->{$c};
        }
        return null;
    }

    // (tuỳ chọn) nếu có category_id/brand_id trong DB
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id', 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class, 'brand_id', 'brand_id');
    }
}
