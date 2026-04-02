<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_id',
    'variant_name',
    'sku',
    'price',
    'sale_price',
    'duration_minutes',
    'includes',
    'max_bookings_per_day',
    'is_active',
    'sort_order',
])]
class ServiceVariant extends Model
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
            'price' => 'decimal:0',
            'sale_price' => 'decimal:0',
            'includes' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the service that owns the variant.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Scope a query to only include active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
