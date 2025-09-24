<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    protected $table = 'favourite';          // bảng có sẵn
    protected $primaryKey = 'favourite_id';  // khóa chính
    public $timestamps = false;              // DB tự set added_date
    protected $fillable = ['user_id', 'product_id', 'added_date'];

    public function user()
    {
        return $this->belongsTo(User::class,  'user_id', 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
