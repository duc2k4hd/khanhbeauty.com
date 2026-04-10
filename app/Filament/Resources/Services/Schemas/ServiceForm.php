<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Services\MediaUploadService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('short_description')
                    ->required(),
                RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('VNĐ'),
                TextInput::make('sale_price')
                    ->numeric()
                    ->prefix('VNĐ'),
                TextInput::make('price_unit')
                    ->required()
                    ->default('buổi'),
                TextInput::make('duration_minutes')
                    ->numeric(),

                // ── Ảnh đại diện → lưu featured_image_id ──────
                FileUpload::make('featured_image_id')
                    ->label('Ảnh đại diện')
                    ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                    ->disk('public')
                    ->directory('services')
                    ->saveUploadedFileUsing(function ($file) {
                        return MediaUploadService::upload($file, 'service_featured');
                    })
                    ->required(),

                // ── Gallery → lưu gallery_ids (mảng IDs) ───────
                FileUpload::make('gallery_ids')
                    ->label('Gallery ảnh')
                    ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                    ->multiple()
                    ->disk('public')
                    ->directory('services/gallery')
                    ->saveUploadedFileUsing(function ($file) {
                        return MediaUploadService::upload($file, 'service_gallery');
                    }),

                TagsInput::make('includes'),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),

                Section::make('SEO Metadata')
                    ->schema([
                        TextInput::make('meta_title'),
                        TextInput::make('meta_description'),
                        TextInput::make('meta_keywords'),
                    ])->collapsible(),
            ]);
    }
}
