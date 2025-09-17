<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'purchase_order_id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'supplier_id',
        'order_date',
        'total_amount',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id', 'purchase_order_id');
    }
}