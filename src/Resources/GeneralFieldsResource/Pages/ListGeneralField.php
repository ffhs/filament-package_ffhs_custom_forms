<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralField extends ListRecords
{
    protected static string $resource = GeneralFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
