<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $timestamps = false;

    protected $fillable = [
        'brand_name',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'products', 'brand_id', 'category_id');
    }

    public function productCount()
    {
        return $this->products()->count();
    }

    public function categoryCount()
    {
        return $this->categories()->distinct()->count();
    }
}