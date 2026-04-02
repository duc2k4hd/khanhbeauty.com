<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'reviewable_type',
    'reviewable_id',
    'user_id',
    'guest_name',
    'rating',
    'title',
    'content',
    'images',
    'is_verified',
    'is_approved',
    'admin_reply',
    'admin_reply_at',
    'helpful_count',
])]
class Review extends Model
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
            'rating' => 'integer',
            'images' => 'array',
            'is_verified' => 'boolean',
            'is_approved' => 'boolean',
            'admin_reply_at' => 'datetime',
        ];
    }

    /**
     * Get the parent reviewable model (Service or Product).
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
