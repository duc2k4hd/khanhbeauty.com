<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'content',
    'template',
    'featured_image_id',
    'og_image_id',
    'is_active',
    'sort_order',
    'meta_title',
    'meta_description',
    'schema_markup',
])]
class Page extends Model
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
            'is_active'     => 'boolean',
            'schema_markup' => 'array',
        ];
    }

    /** Ảnh đại diện trang */
    public function featuredImage()
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /** Ảnh OG */
    public function ogImage()
    {
        return $this->belongsTo(Media::class, 'og_image_id');
    }

    /**
     * Scope a query to only include active pages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
