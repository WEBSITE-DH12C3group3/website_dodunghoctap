<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';
    public $incrementing = false; // Tắt tự động tăng cho product_id
    protected $table = 'products';
    public $timestamps = false; // Bảng chỉ có created_at
    protected $fillable = [
        'product_id',
        'product_name',
        'category_id',
        'brand_id',
        'description',
        'price',
        'stock_quantity',
        'image_url',
        'sold',
        'is_featured'
    ];
    protected $casts = [
        'is_featured' => 'boolean',
    ];

    /** Chỉ lấy record gắn cờ nổi bật rõ ràng */
    public function scopeFeatured($q)
    {
        return $q->where('is_featured', 1);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'product_id', 'product_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'product_id', 'product_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'product_id', 'product_id');
    }

    public function getFavouritesCountAttribute()
    {
        return $this->favourites()->count();
    }
}
