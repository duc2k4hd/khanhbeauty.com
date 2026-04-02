<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'product_id',
    'variant_id',
    'product_name',
    'variant_name',
    'sku',
    'price',
    'quantity',
    'total',
])]
class OrderItem extends Model
{
    use HasFactory;

    /**
     * Disable timestamps if not needed (usually order items don't need independent timestamps).
     * Migration doesn't have timestamps.
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:0',
            'total' => 'decimal:0',
        ];
    }

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the product (original) for the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the variant (original) for the item.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
