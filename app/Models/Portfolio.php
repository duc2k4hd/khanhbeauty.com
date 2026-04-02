<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'description',
    'category',
    'before_image_id',
    'after_image_id',
    'gallery_ids',
    'client_name',
    'services_used',
    'products_used',
    'is_featured',
    'sort_order',
    'meta_title',
    'meta_description',
])]
class Portfolio extends Model
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
            'gallery_ids'   => 'array',
            'services_used' => 'array',
            'products_used' => 'array',
            'is_featured'   => 'boolean',
        ];
    }

    /** Ảnh trước (before) */
    public function beforeImage()
    {
        return $this->belongsTo(Media::class, 'before_image_id');
    }

    /** Ảnh sau (after) */
    public function afterImage()
    {
        return $this->belongsTo(Media::class, 'after_image_id');
    }

    /** Lấy mảng Media từ gallery_ids */
    public function getGalleryMediaAttribute()
    {
        $ids = $this->gallery_ids ?? [];
        if (empty($ids)) return collect();
        return Media::whereIn('id', $ids)->get()->sortBy(fn($m) => array_search($m->id, $ids));
    }

    /**
     * Scope a query to only include featured portfolios.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
