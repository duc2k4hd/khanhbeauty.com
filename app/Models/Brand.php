<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'logo_url',
    'description',
    'website',
    'is_active',
    'sort_order',
    'meta_title',
    'meta_description',
])]
class Brand extends Model
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the products for the brand.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    /**
     * Scope a query to only include active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
