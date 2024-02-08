<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;
use Filament\Forms\Components\TextInput;

class CheckboxListType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings,HasTypeOptions{
        HasTypeOptions::getExtraOptionSchema insteadof HasBasicSettings;
        HasBasicSettings::getExtraOptionSchema as getExtraOptionSchemaBasicSetup;
    }

    public static function getFieldIdentifier(): string { return "checkbox_list"; }

    public function viewModes(): array {
        return  [
            'default'  => CustomFieldType\Views\CheckboxListTypeView::class,
        ];
    }

    public function getExtraOptionSchemaHasOptions() : array{
        return  array_merge($this->getExtraOptionSchemaBasicSetup(),[
            TextInput::make("columns")
                ->label("Spalten")//ToDo Translate
                ->columnStart(1)
                ->numeric()
        ]);
    }

    protected function getExtraOptionFieldsBasicOptions(): array {
        return [
            'columns'=> 1,
        ];
    }


    public function icon(): String {
        return  "bi-ui-checks-grid";
    }
}
