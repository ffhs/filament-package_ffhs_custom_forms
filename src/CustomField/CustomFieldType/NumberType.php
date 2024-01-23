<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\NumberTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Filament\Forms\Components\TextInput;

class NumberType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "number";}



    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("min_value")
                ->label("Mindestgrösse")//ToDo Translation
                ->step(1)
                ->required()
                ->integer(),
            TextInput::make("max_value")
                ->label("Maximale Grösse") //ToDo Translate
                ->step(1)
                ->required()
                ->integer(),

            $this->getColumnSpanOption(),
            $this->getNewLineOption()->columnStart(1),
            $this->getInLineLabelOption()
        ];
    }

    public function getExtraOptionFields(): array {
        return [
            'column_span' => 3,
            'in_line_label' => false,
            'new_line_option' => true,
            'min_value'=>0,
            'max_value'=>100,
        ];
    }

    public function viewModes(): array {
        return [
          'default' => NumberTypeView::class
        ];
    }

    public function icon(): string {
        return  "tabler-number";
    }
}
