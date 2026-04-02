<?php

namespace App\Filament\Resources\Products\Tables;

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

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('brand.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('short_description')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('low_stock_threshold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight_grams')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('featured_image'),
                TextColumn::make('video_url')
                    ->searchable(),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('is_digital')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sold_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('review_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('meta_title')
                    ->searchable(),
                TextColumn::make('meta_description')
                    ->searchable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
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
