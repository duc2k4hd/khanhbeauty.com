<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'type',
    'sort_order',
])]
class ProductAttribute extends Model
{
    use HasFactory;

    /**
     * Get the values for the attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id')->orderBy('sort_order');
    }
}
