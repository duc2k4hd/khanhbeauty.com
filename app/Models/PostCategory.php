<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'parent_id',
    'name',
    'slug',
    'description',
    'image_id',
    'sort_order',
    'is_active',
    'meta_title',
    'meta_description',
])]
class PostCategory extends Model
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
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    /**
     * Get the children categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(PostCategory::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get the posts that belong to the category.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category_map', 'category_id', 'post_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Ảnh danh mục từ media */
    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
