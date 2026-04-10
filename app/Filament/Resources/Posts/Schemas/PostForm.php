<?php

namespace App\Filament\Resources\Posts\Schemas;

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

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('author_id')
                    ->relationship('author', 'full_name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('excerpt')
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),

                Section::make('Hình ảnh bài viết')
                    ->schema([
                        // ── Ảnh đại diện → lưu featured_image_id ──
                        FileUpload::make('featured_image_id')
                            ->label('Ảnh đại diện bài viết')
                            ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                            ->disk('public')
                            ->directory('posts')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'post_featured');
                            }),
                    ]),

                Section::make('Xuất bản')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'published' => 'Published',
                                'scheduled' => 'Scheduled',
                                'archived'  => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),
                        DateTimePicker::make('published_at'),
                        Toggle::make('is_featured'),
                        Toggle::make('allow_comments')
                            ->default(true),
                    ])->columns(2),

                Section::make('SEO & Social')
                    ->schema([
                        TextInput::make('meta_title'),
                        Textarea::make('meta_description'),
                        TextInput::make('meta_keywords'),
                        TextInput::make('focus_keyword'),
                        TextInput::make('og_title'),
                        Textarea::make('og_description'),
                        // ── Ảnh OG → lưu og_image_id ───────────────
                        FileUpload::make('og_image_id')
                            ->label('Ảnh OG (Social Sharing)')
                            ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                            ->disk('public')
                            ->directory('posts/og')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'post_og');
                            }),
                        TextInput::make('canonical_url')
                            ->url(),
                    ])->collapsible(),
            ]);
    }
}
