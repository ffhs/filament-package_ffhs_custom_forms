<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\TemplateResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Contracts\Support\Htmlable;

class CreateTemplate extends CreateCustomForm
{
    protected static string $resource = TemplateResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomForm::__('pages.create_template.title');
    }
}
