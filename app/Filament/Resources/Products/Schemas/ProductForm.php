<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Services\MediaUploadService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('brand_id')
                    ->relationship('brand', 'name'),
                Section::make('Thông tin chính')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->required(),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->required(),
                        TextInput::make('short_description'),
                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Giá & Kho hàng')
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('VNĐ'),
                        TextInput::make('sale_price')
                            ->numeric()
                            ->prefix('VNĐ'),
                        TextInput::make('cost_price')
                            ->numeric()
                            ->prefix('VNĐ'),
                        TextInput::make('stock_quantity')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('low_stock_threshold')
                            ->required()
                            ->numeric()
                            ->default(5),
                    ])->columns(2),

                Section::make('Hình ảnh & Video')
                    ->schema([
                        // ── Ảnh đại diện → lưu featured_image_id ──
                        FileUpload::make('featured_image_id')
                            ->label('Ảnh đại diện sản phẩm')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                            ->disk('public')
                            ->directory('products')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'product_featured');
                            })
                            ->required(),

                        // ── Gallery → lưu gallery_ids ──────────────
                        FileUpload::make('gallery_ids')
                            ->label('Gallery ảnh sản phẩm')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                            ->multiple()
                            ->disk('public')
                            ->directory('products/gallery')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'product_gallery');
                            }),

                        TextInput::make('video_url')
                            ->url(),
                    ]),

                Section::make('Trạng thái')
                    ->schema([
                        Toggle::make('is_featured'),
                        Toggle::make('is_active')
                            ->default(true),
                        Toggle::make('is_digital'),
                        DateTimePicker::make('published_at'),
                    ])->columns(2),

                Section::make('SEO Metadata')
                    ->schema([
                        TextInput::make('meta_title'),
                        Textarea::make('meta_description'),
                        Textarea::make('schema_markup'),
                    ])->collapsible(),
            ]);
    }
}
