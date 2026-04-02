<?php

namespace App\Filament\Resources\ServiceVariants\Pages;

use App\Filament\Resources\ServiceVariants\ServiceVariantResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceVariant extends ViewRecord
{
    protected static string $resource = ServiceVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
