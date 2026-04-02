<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use App\Services\MediaUploadService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostCategoryForm
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
                // ── Ảnh danh mục → lưu image_id ────────────────
                FileUpload::make('image_id')
                    ->label('Ảnh đại diện danh mục')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                    ->disk('public')
                    ->directory('categories/posts')
                    ->saveUploadedFileUsing(function ($file) {
                        return MediaUploadService::upload($file, 'post_category');
                    }),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
            ]);
    }
}
