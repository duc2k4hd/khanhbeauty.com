<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'author_id',
    'title',
    'slug',
    'excerpt',
    'content',
    'featured_image_id',
    'og_image_id',
    'status',
    'published_at',
    'scheduled_at',
    'view_count',
    'reading_time_min',
    'is_featured',
    'allow_comments',
    'meta_title',
    'meta_description',
    'meta_keywords',
    'og_title',
    'og_description',
    'canonical_url',
    'focus_keyword',
    'secondary_keywords',
    'internal_links_count',
    'external_links_count',
    'word_count',
    'schema_markup',
])]
class Post extends Model
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
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'secondary_keywords' => 'array',
            'schema_markup' => 'array',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    /** Ảnh đại diện bài viết */
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /** Ảnh OG cho social sharing */
    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_image_id');
    }

    /**
     * Get the author (user) of the post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the categories for the post.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'post_category_map', 'post_id', 'category_id');
    }

    /**
     * Get the tags for the post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag_map', 'post_id', 'tag_id');
    }

    /**
     * Get the reviews (comments) for the post.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope a query to only include featured posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
