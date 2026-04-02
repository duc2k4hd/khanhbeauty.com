<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('parent.name')
                    ->label('Parent')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('image_url')
                    ->placeholder('-'),
                TextEntry::make('sort_order')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('meta_title')
                    ->placeholder('-'),
                TextEntry::make('meta_description')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
