<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'short_description',
    'description',
    'price',
    'sale_price',
    'price_unit',
    'duration_minutes',
    'featured_image_id',
    'gallery_ids',
    'video_url',
    'includes',
    'is_featured',
    'is_active',
    'sort_order',
    'view_count',
    'meta_title',
    'meta_description',
    'meta_keywords',
    'schema_markup',
    'benefits',
    'process_steps',
])]
class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected ?string $cacheOriginalSlug = null;

    protected ?int $cacheOriginalCategoryId = null;

    protected static function booted()
    {
        $rememberOriginalState = function (self $service): void {
            $service->cacheOriginalSlug = $service->getOriginal('slug') ?: $service->slug;
            $service->cacheOriginalCategoryId = $service->getOriginal('category_id') ?: $service->category_id;
        };

        static::updating($rememberOriginalState);
        static::deleting($rememberOriginalState);
        static::saved(fn (self $service) => $service->flushRelevantCaches());
        static::deleted(fn (self $service) => $service->flushRelevantCaches());
        static::restored(fn (self $service) => $service->flushRelevantCaches());
    }

    public function flushRelevantCaches(): void
    {
        $slugs = array_filter(array_unique([
            $this->cacheOriginalSlug,
            $this->slug,
        ]));

        foreach ($slugs as $slug) {
            Cache::forget('service_v7_safe_' . $slug);
            Cache::forget('service_page_' . $slug);
        }

        $categoryIds = array_filter(array_unique([
            $this->cacheOriginalCategoryId,
            $this->category_id,
        ]));

        foreach ($categoryIds as $categoryId) {
            Cache::forget('related_v7_' . $categoryId);
        }

        Cache::forget('services_index');
        Cache::forget('services_index_safe');
        Cache::forget('home_v8_safe');
        Cache::forget('home_data');

        $this->cacheOriginalSlug = $this->slug;
        $this->cacheOriginalCategoryId = $this->category_id;
    }

    protected function casts(): array
    {
        return [
            'price'         => 'decimal:0',
            'sale_price'    => 'decimal:0',
            'gallery_ids'   => 'array',
            'includes'      => 'array',
            'schema_markup' => 'array',
            'benefits'      => 'array',
            'process_steps' => 'array',
            'is_featured'   => 'boolean',
            'is_active'     => 'boolean',
        ];
    }

    /** Ảnh đại diện lấy từ bảng media */
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /** Danh mục */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /** Các gói biến thể */
    public function variants(): HasMany
    {
        return $this->hasMany(ServiceVariant::class, 'service_id');
    }

    /** FAQs liên quan */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'related_service_id');
    }

    /** Đánh giá */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Lấy mảng Media objects từ gallery_ids.
     * Trả về Collection<Media>.
     */
    public function getGalleryMediaAttribute()
    {
        $ids = $this->gallery_ids ?? [];
        if (empty($ids)) return collect();
        return Media::whereIn('id', $ids)->get()->sortBy(fn($m) => array_search($m->id, $ids));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
