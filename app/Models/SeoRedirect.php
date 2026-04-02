<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'from_url',
    'to_url',
    'status_code',
    'hit_count',
    'is_active',
])]
class SeoRedirect extends Model
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
            'status_code' => 'integer',
            'hit_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active redirects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
