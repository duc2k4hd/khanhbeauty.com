<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'question',
    'answer',
    'category',
    'related_service_id',
    'sort_order',
    'is_active',
    'schema_included',
    'view_count',
])]
class Faq extends Model
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
            'schema_included' => 'boolean',
        ];
    }

    /**
     * Get the related service for the FAQ.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'related_service_id');
    }

    /**
     * Scope a query to only include active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
