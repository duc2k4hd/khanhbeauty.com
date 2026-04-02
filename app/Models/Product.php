<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id',
    'brand_id',
    'name',
    'slug',
    'sku',
    'short_description',
    'description',
    'price',
    'sale_price',
    'cost_price',
    'stock_quantity',
    'low_stock_threshold',
    'weight_grams',
    'featured_image_id',
    'gallery_ids',
    'video_url',
    'is_featured',
    'is_active',
    'is_digital',
    'sort_order',
    'view_count',
    'sold_count',
    'avg_rating',
    'review_count',
    'meta_title',
    'meta_description',
    'schema_markup',
    'published_at',
])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price'         => 'decimal:0',
            'sale_price'    => 'decimal:0',
            'cost_price'    => 'decimal:0',
            'gallery_ids'   => 'array',
            'schema_markup' => 'array',
            'is_featured'   => 'boolean',
            'is_active'     => 'boolean',
            'is_digital'    => 'boolean',
            'published_at'  => 'datetime',
            'avg_rating'    => 'float',
        ];
    }

    /** Ảnh đại diện lấy từ bảng media */
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /** Lấy mảng Media từ gallery_ids */
    public function getGalleryMediaAttribute()
    {
        $ids = $this->gallery_ids ?? [];
        if (empty($ids)) return collect();
        return Media::whereIn('id', $ids)->get()->sortBy(fn($m) => array_search($m->id, $ids));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
