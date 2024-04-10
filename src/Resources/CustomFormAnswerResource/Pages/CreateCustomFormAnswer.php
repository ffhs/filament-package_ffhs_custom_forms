<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Query\Builder;

class CreateCustomFormAnswer extends CreateRecord
{
    protected static string $resource = CustomFormAnswerResource::class;

    public function form(Form $form): Form {
        return $form->schema([
            TextInput::make("short_title")
                ->label("Name"), //ToDo Translate
            Select::make("custom_form_id")
                ->label("Formular") //ToDo Translate
                ->relationship("customForm","short_title", fn( $query) => $query->where("is_template",false))
                ->required(),
        ]);
    }


}
