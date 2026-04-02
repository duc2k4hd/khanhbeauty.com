<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'type',
    'value',
    'min_order_amount',
    'max_discount_amount',
    'start_date',
    'end_date',
    'usage_limit',
    'used_count',
    'is_active',
])]
class Coupon extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the orders for the coupon.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Check if the coupon is valid for a given amount.
     */
    public function isValidForAmount($amount): bool
    {
        if (!$this->is_active) return false;
        if ($this->start_date && $this->start_date->isFuture()) return false;
        if ($this->end_date && $this->end_date->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->min_order_amount && $amount < $this->min_order_amount) return false;
        
        return true;
    }
}
