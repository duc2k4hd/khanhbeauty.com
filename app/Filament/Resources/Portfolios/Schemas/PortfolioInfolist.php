<?php

namespace App\Filament\Resources\Portfolios\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PortfolioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('category')
                    ->placeholder('-'),
                ImageEntry::make('before_image')
                    ->placeholder('-'),
                ImageEntry::make('after_image'),
                TextEntry::make('client_name')
                    ->placeholder('-'),
                IconEntry::make('is_featured')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
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
