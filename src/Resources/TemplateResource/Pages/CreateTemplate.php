<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class CreateTemplate extends CreateCustomForm
{
    protected static string $resource = TemplateResource::class;



    public function form(Form $form): Form {
        return $form
            ->schema([
                TextInput::make("template_identifier")
                    ->label("Template Id")
                    ->required(), //ToDo Translate
                TextInput::make("short_title")
                    ->label("Namen")
                    ->required(), //ToDo Translate
                Select::make("custom_form_identifier")
                    ->label("Formularart") //ToDo Translate
                    ->required()
                    ->options(function (){
                        $keys = array_map(fn($config) => $config::identifier(),config("ffhs_custom_forms.forms"));
                        $values = array_map(fn($config) => $config::displayName(),config("ffhs_custom_forms.forms"));
                        return array_combine($keys,$values);
                    }),
            ]);
    }

}
