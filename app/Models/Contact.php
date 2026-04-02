<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'phone',
    'subject',
    'message',
    'source_page',
    'is_read',
    'admin_notes',
])]
class Contact extends Model
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
            'is_read' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include unread contacts.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
