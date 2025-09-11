<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateCustomForm extends CreateRecord
{
    protected static string $resource = CustomFormResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomForm::__('pages.create.title');
    }
}
