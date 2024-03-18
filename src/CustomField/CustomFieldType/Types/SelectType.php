<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\HasTypeOptions;

class SelectType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;
    use HasTypeOptions;

    public static function getFieldIdentifier(): string { return "select"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\SelectTypeView::class,
        ];
    }
    public function icon(): String {
        return  "carbon-select-window";
    }

  /*  public function getExtraOptionSchemaHasOptions() : array{
        return array_merge($this->getExtraOptionSchemaBasicSetup(), [
            Toggle::make("several")
                ->label("Mehre auswählbar")//ToDo Translate
                ->columnSpanFull()
                ->live(),

            TextInput::make("min_select")
                ->hidden(fn($get)=> !$get("several"))
                ->label("Mindestanzahl") //ToDo Translate
                ->columnStart(1)
                ->helperText("Greift nur bei (Benötigt)")//ToDo Translate
                ->minValue(0)
                ->step(1)
                ->required()
                ->numeric(),

            TextInput::make("max_select")
                ->hidden(fn($get)=> !$get("several"))
                ->label("Maximalanzahl") //ToDo Translate
                ->helperText("'0' entspricht keine Begrenzung") //ToDo Translate
                ->minValue(0)
                ->step(1)
                ->required()
                ->numeric(),
        ]);
    }
*/

   /* public function getExtraOptionFieldsBasicOptions():array{
        return [
            'several' => false,
            'min_select'=>1,
            'max_select'=> 0,
        ];
    }*/



}
