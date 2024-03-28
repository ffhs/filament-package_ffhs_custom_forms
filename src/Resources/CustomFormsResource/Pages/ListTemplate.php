<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplate extends ListRecords
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
