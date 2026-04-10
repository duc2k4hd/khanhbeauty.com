<?php

namespace App\Filament\Resources\ServiceCategories\Schemas;

use App\Services\MediaUploadService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->relationship('parent', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('icon'),

                // ── Ảnh danh mục → lưu image_id ────────────────
                FileUpload::make('image_id')
                    ->label('Ảnh đại diện danh mục')
                    ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                    ->disk('public')
                    ->directory('categories/services')
                    ->saveUploadedFileUsing(function ($file) {
                        return MediaUploadService::upload($file, 'service_category');
                    }),

                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
                TextInput::make('meta_keywords'),
            ]);
    }
}
