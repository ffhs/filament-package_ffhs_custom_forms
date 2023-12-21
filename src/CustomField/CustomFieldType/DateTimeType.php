<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\DateTimeTypeView;
use Filament\Forms\Components\TextInput;

class DateTimeType extends CustomFieldType
{
    public static function getFieldIdentifier(): string {return "date-time";}


    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("format")
                ->placeholder("Y-m-d H:i:s")
                ->columnSpanFull()

        ];
    }
    public function getExtraOptionFields(): array {
        return [
            'format'=>null,
        ];
    }

    public function viewModes(): array {
        return  [
            'default'  => DateTimeTypeView::class
        ];
    }

}
