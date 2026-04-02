<?php

namespace App\Filament\Resources\ServiceVariants\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceVariantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('service.name')
                    ->label('Service'),
                TextEntry::make('variant_name'),
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('sale_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('duration_minutes')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_bookings_per_day')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
