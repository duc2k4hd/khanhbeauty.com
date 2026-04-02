<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'order_code',
    'customer_id',
    'guest_name',
    'guest_phone',
    'guest_email',
    'shipping_address',
    'shipping_province',
    'shipping_district',
    'shipping_ward',
    'subtotal',
    'shipping_fee',
    'discount_amount',
    'total_amount',
    'coupon_id',
    'status',
    'payment_status',
    'payment_method',
    'shipping_method',
    'tracking_code',
    'notes',
    'admin_notes',
    'source',
    'completed_at',
])]
class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:0',
            'shipping_fee' => 'decimal:0',
            'discount_amount' => 'decimal:0',
            'total_amount' => 'decimal:0',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the customer (user) for the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the coupon used for the order.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}
