<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subtotal',
        'tax',
        'total',
        'payment_method',
        'status',
        'refunded_amount',
        'refund_reason',
        'cancelled_at',
        'refunded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the user that made the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Reduce inventory stock for completed order.
     */
    public function reduceInventory(): void
    {
        foreach ($this->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)->first();
            if ($inventory) {
                $inventory->decreaseStock($item->quantity);
            }
        }
    }

    /**
     * Restore inventory stock for cancelled order.
     */
    public function restoreInventory(): void
    {
        foreach ($this->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)->first();
            if ($inventory) {
                $inventory->increaseStock($item->quantity);
            }
        }
    }
}