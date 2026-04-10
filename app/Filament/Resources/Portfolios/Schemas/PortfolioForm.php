<?php

namespace App\Filament\Resources\Portfolios\Schemas;

use App\Services\MediaUploadService;
use Filament\Schemas\Components\FileUpload;
use Filament\Schemas\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\TagsInput;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;

class PortfolioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                TextInput::make('category'),

                Section::make('Hình ảnh dự án')
                    ->schema([
                        // ── Ảnh Before → lưu before_image_id ──────
                        FileUpload::make('before_image_id')
                            ->label('Ảnh trước (Before)')
                            ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                            ->disk('public')
                            ->directory('portfolios/before')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'portfolio_before');
                            }),

                        // ── Ảnh After → lưu after_image_id ─────────
                        FileUpload::make('after_image_id')
                            ->label('Ảnh sau (After)')
                            ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                            ->disk('public')
                            ->directory('portfolios/after')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'portfolio_after');
                            })
                            ->required(),

                        // ── Gallery → lưu gallery_ids ───────────────
                        FileUpload::make('gallery_ids')
                            ->label('Bộ sưu tập ảnh khác')
                            ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                            ->multiple()
                            ->disk('public')
                            ->directory('portfolios/gallery')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'portfolio_gallery');
                            }),
                    ]),

                Section::make('Chi tiết kỹ thuật')
                    ->schema([
                        TextInput::make('client_name'),
                        TagsInput::make('services_used')
                            ->label('Dịch vụ đã thực hiện'),
                        TagsInput::make('products_used')
                            ->label('Sản phẩm đã dùng'),
                    ])->columns(2),

                Section::make('Cài đặt & SEO')
                    ->schema([
                        Toggle::make('is_featured'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        TextInput::make('meta_title'),
                        Textarea::make('meta_description'),
                    ])->collapsible(),
            ]);
    }
}
