<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomForm extends CreateRecord
{
    use FormForm;
    protected static string $resource = CustomFormResource::class;

    public function form(Form $form): Form {
        return $form
            ->schema([
                TextInput::make("short_title"),
                Select::make("custom_form_identifier")
                    ->options(function (){
                        $keys = array_map(fn($config) => $config::identifier(),config("ffhs_custom_forms.forms"));
                        $values = array_map(fn($config) => $config::displayName(),config("ffhs_custom_forms.forms"));
                        return array_combine($keys,$values);
                    }),
            ]);
    }


}
