<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'attribute_id',
    'value',
    'color_code',
    'image_id',
    'sort_order',
])]
class ProductAttributeValue extends Model
{
    use HasFactory;

    /**
     * Get the attribute that owns the value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Get the variants that have this attribute value.
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values', 'attribute_value_id', 'variant_id');
    }

    /** Ảnh giá trị thuộc tính từ media */
    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
