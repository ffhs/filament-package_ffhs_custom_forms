<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\DateTypeView;
use Filament\Forms\Components\TextInput;

class DateType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    public static function getFieldIdentifier(): string {return "date";}


    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("format")
                ->placeholder("Y-m-d")
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
            'default'  => DateTypeView::class
        ];
    }

}
