<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';
    public $timestamps = false; // dùng cột comment_date

    protected $fillable = ['product_id', 'user_id', 'rating', 'comment', 'comment_date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
