<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'uploader_id',
    'file_name',
    'file_path',
    'file_url',
    'disk',
    'mime_type',
    'file_size_bytes',
    'width',
    'height',
    'alt_text',
    'title',
    'caption',
    'folder',
    'thumbnails',
    'is_optimized',
    'image_type',
])]
class Media extends Model
{
    use HasFactory;

    /**
     * Disable updated_at if not needed (usually media is only created).
     * Migration doesn't have updated_at.
     */
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'thumbnails' => 'array',
            'is_optimized' => 'boolean',
            'file_size_bytes' => 'integer',
        ];
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
