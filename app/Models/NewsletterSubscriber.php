<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'email',
    'name',
    'is_active',
    'subscribed_at',
    'unsubscribed_at',
])]
class NewsletterSubscriber extends Model
{
    use HasFactory;

    /**
     * Disable timestamps if not needed.
     * Migration doesn't have standard timestamps.
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include active subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
