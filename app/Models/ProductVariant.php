<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'product_id',
    'sku',
    'variant_name',
    'price',
    'sale_price',
    'cost_price',
    'stock_quantity',
    'image_id',
    'barcode',
    'weight_grams',
    'is_active',
    'sort_order',
])]
class ProductVariant extends Model
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
            'price' => 'decimal:0',
            'sale_price' => 'decimal:0',
            'cost_price' => 'decimal:0',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the attribute values that define this variant.
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'variant_attribute_values', 'variant_id', 'attribute_value_id');
    }

    /**
     * Check if variant is in stock.
     */
    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /** Ảnh variant từ media */
    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
