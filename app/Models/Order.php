<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'order_date',
        'status',
        'total_amount',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'order_id', 'order_id');
    }

    /**
     * Determine if a given status should count towards sold quantities.
     */
    public static function isPaidStatus(?string $status): bool
    {
        return in_array($status, ['confirmed', 'completed', 'delivered'], true);
    }

    /**
     * Adjust product sold counters for every order item.
     */
    public function adjustProductSold(int $direction): void
    {
        if (!in_array($direction, [1, -1], true)) {
            throw new \InvalidArgumentException('Direction must be 1 or -1.');
        }

        $this->loadMissing(['items.product']);

        foreach ($this->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            $quantity = (int) ($item->quantity ?? 0);
            if ($quantity <= 0) {
                continue;
            }

            if ($direction === 1) {
                $product->increment('sold', $quantity);
                continue;
            }

            $current = (int) ($product->sold ?? 0);
            $newValue = max(0, $current - $quantity);

            if ($newValue !== $current) {
                $product->update(['sold' => $newValue]);
            }
        }
    }
    public function adjustProductStock(int $direction): void
    {
        // direction: -1 = trừ kho (khi đã thanh toán), +1 = hoàn kho (khi hủy/refund sau paid)
        if (!in_array($direction, [1, -1], true)) {
            throw new \InvalidArgumentException('Direction must be 1 or -1.');
        }

        $this->loadMissing(['items.product']);

        foreach ($this->items as $item) {
            $product = $item->product;
            if (!$product) continue;

            $qty = (int) ($item->quantity ?? 0);
            if ($qty <= 0) continue;

            if ($direction === -1) {
                // Trừ kho nhưng không cho âm: stock_quantity = GREATEST(stock_quantity - qty, 0)
                $expr = "GREATEST(stock_quantity - {$qty}, 0)";
                Product::whereKey($product->getKey())
                    ->update(['stock_quantity' => DB::raw($expr)]);
            } else {
                // Hoàn kho
                $product->increment('stock_quantity', $qty);
            }
        }
    }
    /**
     * Sync sold counters based on a status transition.
     */
    public function syncProductSoldForStatusChange(?string $previousStatus, ?string $nextStatus): void
    {
        $wasPaid = self::isPaidStatus($previousStatus);
        $isPaid = self::isPaidStatus($nextStatus);

        if ($wasPaid === $isPaid) return;

        // Chuyển unpaid -> paid: tăng sold, trừ kho
        if (!$wasPaid && $isPaid) {
            $this->adjustProductSold(+1);
            $this->adjustProductStock(-1);
            return;
        }

        // Chuyển paid -> unpaid (cancel/refund): giảm sold, hoàn kho
        // Bật nếu chính sách của bạn cần hoàn kho khi hủy sau khi đã paid:
        $this->adjustProductSold(-1);
        $this->adjustProductStock(+1);
    }
}
