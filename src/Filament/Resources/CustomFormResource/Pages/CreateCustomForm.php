<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomForm extends CreateRecord
{
    protected static string $resource = CustomFormResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('short_title')
                    ->label('Name') //ToDo Translate
                    ->required(),
                CustomFormTypeSelector::make()
                    ->required()
            ]);
    }
}
