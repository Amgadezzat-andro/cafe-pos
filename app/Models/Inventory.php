<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'low_stock_threshold',
        'reorder_level',
        'notes',
        'last_stock_check',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_stock_check' => 'datetime',
    ];

    /**
     * Get the product that owns this inventory record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if product is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if product needs reordering.
     */
    public function needsReorder(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Get stock status for display.
     */
    public function getStockStatus(): string
    {
        if ($this->quantity <= 0) {
            return 'Out of Stock';
        } elseif ($this->needsReorder()) {
            return 'Needs Reorder';
        } elseif ($this->isLowStock()) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    /**
     * Get stock status badge color.
     */
    public function getStockStatusColor(): string
    {
        return match ($this->getStockStatus()) {
            'Out of Stock' => 'red',
            'Needs Reorder' => 'orange',
            'Low Stock' => 'yellow',
            default => 'green',
        };
    }

    /**
     * Reduce inventory quantity.
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->quantity < $quantity) {
            return false; // Insufficient stock
        }

        $this->quantity -= $quantity;
        $this->save();

        return true;
    }

    /**
     * Increase inventory quantity.
     */
    public function increaseStock(int $quantity): bool
    {
        $this->quantity += $quantity;
        $this->save();

        return true;
    }

    /**
     * Set stock to specific quantity.
     */
    public function setStock(int $quantity): bool
    {
        $this->quantity = max(0, $quantity);
        $this->last_stock_check = now();
        $this->save();

        return true;
    }
}
