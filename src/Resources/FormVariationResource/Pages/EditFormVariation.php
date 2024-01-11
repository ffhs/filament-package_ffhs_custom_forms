<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\FormVariationResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\FormVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormVariation extends EditRecord
{
    protected static string $resource = FormVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
