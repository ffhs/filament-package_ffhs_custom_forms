<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\TextTypeView;
use Filament\Forms\Components\TextInput;

class TextType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {return "text";}


    public function getExtraOptionFields(): array {
        return [
          'max_length' => 100,
          'min_length' => 0,
          'column_span' => 3
        ];
    }

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
            TextInput::make("max_length")
                ->columnStart(1)
                ->step(1)
                ->required()
                ->integer(),
            TextInput::make("min_length")
                ->step(1)
                ->required()
                ->integer(),
        ];
    }


    public function viewModes(): array {
        return [
          'default' => TextTypeView::class
        ];
    }
}
