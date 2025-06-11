<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateCustomForm extends CreateRecord
{
    protected static string $resource = CustomFormResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomForm::__('pages.create.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('short_title')
                    ->label(CustomForm::__('attributes.short_title'))
                    ->required(),
                CustomFormTypeSelector::make()
                    ->required()
            ]);
    }
}
