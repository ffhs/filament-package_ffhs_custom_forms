<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnsweResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomFormAnswer extends CreateRecord
{
    protected static string $resource = CustomFormAnsweResource::class;

    public function form(Form $form): Form {
        return $form->schema([
            TextInput::make("short_title")
                ->label("Name") //ToDo Translate
                ->required(),
            Select::make("custom_form_id")
                ->label("Fomular") //ToDo Translate
                ->relationship("customForm","short_title")
                ->required(),
        ]);
    }


}
