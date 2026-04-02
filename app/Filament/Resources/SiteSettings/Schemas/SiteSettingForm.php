<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('setting_key')
                    ->required(),
                Textarea::make('setting_value')
                    ->columnSpanFull(),
                TextInput::make('setting_group')
                    ->required()
                    ->default('general'),
            ]);
    }
}
