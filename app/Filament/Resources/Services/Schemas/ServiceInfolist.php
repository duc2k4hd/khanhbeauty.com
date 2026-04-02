<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('short_description'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('sale_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('price_unit'),
                TextEntry::make('duration_minutes')
                    ->numeric()
                    ->placeholder('-'),
                ImageEntry::make('featured_image'),
                IconEntry::make('is_featured')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('view_count')
                    ->numeric(),
                TextEntry::make('booking_count')
                    ->numeric(),
                TextEntry::make('avg_rating')
                    ->numeric(),
                TextEntry::make('meta_title')
                    ->placeholder('-'),
                TextEntry::make('meta_description')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Service $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
