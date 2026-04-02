<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('brand.name')
                    ->label('Brand')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('short_description')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('sale_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('cost_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('stock_quantity')
                    ->numeric(),
                TextEntry::make('low_stock_threshold')
                    ->numeric(),
                TextEntry::make('weight_grams')
                    ->numeric()
                    ->placeholder('-'),
                ImageEntry::make('featured_image'),
                TextEntry::make('video_url')
                    ->placeholder('-'),
                IconEntry::make('is_featured')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_digital')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('view_count')
                    ->numeric(),
                TextEntry::make('sold_count')
                    ->numeric(),
                TextEntry::make('avg_rating')
                    ->numeric(),
                TextEntry::make('review_count')
                    ->numeric(),
                TextEntry::make('meta_title')
                    ->placeholder('-'),
                TextEntry::make('meta_description')
                    ->placeholder('-'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Product $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
