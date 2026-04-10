<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Services\MediaUploadService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin cá nhân')
                    ->schema([
                        TextInput::make('full_name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->tel(),
                        FileUpload::make('avatar_id')
                            ->label('Ảnh đại diện')
                            ->acceptedFileTypes(MediaUploadService::acceptedImageFileTypesForForms())
                            ->disk('public')
                            ->directory('users/avatars')
                            ->saveUploadedFileUsing(function ($file) {
                                return MediaUploadService::upload($file, 'user_avatar');
                            }),
                    ])->columns(2),

                Section::make('Bảo mật & Quyền hạn')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Section::make('Xác thực & Metadata')
                    ->schema([
                        DateTimePicker::make('email_verified_at'),
                        DateTimePicker::make('phone_verified_at'),
                        DateTimePicker::make('last_login_at')
                            ->disabled(),
                    ])->columns(2)->collapsible(),
            ]);
    }
}
