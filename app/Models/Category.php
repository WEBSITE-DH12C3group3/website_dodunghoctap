<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $incrementing = false; // Tắt tự động tăng cho category_id
    protected $table = 'categories';
    public $timestamps = false; // Bảng chỉ có created_at
    protected $fillable = [
        'category_id',
        'category_name',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
      public function brands()
    {
        return $this->belongsToMany(Brand::class, 'products', 'category_id', 'brand_id');
    }
}