<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('uuid')
                    ->label('UUID'),
                TextEntry::make('full_name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('avatar_url')
                    ->placeholder('-'),
                TextEntry::make('role')
                    ->badge(),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('phone_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('last_login_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('provider')
                    ->placeholder('-'),
                TextEntry::make('provider_id')
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
