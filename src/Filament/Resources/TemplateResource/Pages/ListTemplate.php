<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\ListCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\CreateAction;
use Illuminate\Contracts\Support\Htmlable;

class ListTemplate extends ListCustomForm
{
    protected static string $resource = TemplateResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomForm::__('pages.list_template.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
