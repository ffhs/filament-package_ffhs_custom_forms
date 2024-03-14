<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Filament\Forms\Components\TextInput;

class CheckboxListType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings,HasTypeOptions{
        HasTypeOptions::getExtraOptionSchema insteadof HasBasicSettings;
    }

    public static function getFieldIdentifier(): string { return "checkbox_list"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\CheckboxListTypeView::class,
        ];
    }

    /*public function getExtraOptionSchemaHasOptions() : array{
        return  array_merge($this->getExtraOptionSchemaBasicSetup(),[
            TextInput::make("columns")
                ->label("Spalten")//ToDo Translate
                ->columnStart(1)
                ->required()
                ->numeric()
        ]);
    }*/

    protected function getExtraOptionFieldsBasicOptions(): array {
        return [
            'columns'=> 1,
        ];
    }


    public function icon(): String {
        return  "bi-ui-checks-grid";
    }
}
