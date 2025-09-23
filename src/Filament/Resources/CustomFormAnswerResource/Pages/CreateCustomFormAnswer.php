<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateCustomFormAnswer extends CreateRecord
{
    protected static string $resource = CustomFormAnswerResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomFormAnswer::__('pages.create.title');
    }
}
