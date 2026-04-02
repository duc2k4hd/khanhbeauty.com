<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('short_description')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('price_unit')
                    ->searchable(),
                TextColumn::make('duration_minutes')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('featured_image'),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('booking_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('meta_title')
                    ->searchable(),
                TextColumn::make('meta_description')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
