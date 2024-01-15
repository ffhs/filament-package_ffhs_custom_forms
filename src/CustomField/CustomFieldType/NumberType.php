<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\NumberTypeView;
use Filament\Forms\Components\TextInput;

class NumberType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    public static function getFieldIdentifier(): string {return "number";}



    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("column_span")
                ->label("Zeilenweite")//ToDo Translation
                ->maxValue(10)
                ->minValue(1)
                ->columnSpanFull()
                ->step(1)
                ->integer()
                ->required(),
            TextInput::make("step") // ToDo: Translate
                ->numeric()
                ->placeholder(1)
                ->step(1),
            TextInput::make("max_value")
                ->columnStart(1)
                ->step(1)
                ->required()
                ->integer(),
            TextInput::make("min_value")
                ->step(1)
                ->required()
                ->integer(),
        ];
    }

    public function getExtraOptionFields(): array {
        return [
            'column_span' => 2,
            'step'=>1,
            'min_value'=>0,
            'max_value'=>100,
        ];
    }

    public function viewModes(): array {
        return [
          'default' => NumberTypeView::class
        ];
    }
}
