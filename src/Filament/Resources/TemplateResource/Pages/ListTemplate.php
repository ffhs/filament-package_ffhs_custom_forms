<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\ListCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource;
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
