<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\DateTimeTypeView;
use Filament\Forms\Components\TextInput;

class DateTimeType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    public static function getFieldIdentifier(): string {return "date-time";}


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
            TextInput::make("format")
                ->placeholder("Y-m-d H:i:s")
                ->columnSpanFull()

        ];
    }
    public function getExtraOptionFields(): array {
        return [
            'format'=>null,
            'column_span' => 3,
        ];
    }

    public function viewModes(): array {
        return  [
            'default'  => DateTimeTypeView::class
        ];
    }

}
