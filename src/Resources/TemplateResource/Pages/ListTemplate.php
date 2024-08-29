<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\ListCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Filament\Actions;

class ListTemplate extends ListCustomForm
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
