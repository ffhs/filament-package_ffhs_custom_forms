<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;



use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Views\SectionTypeView;
use Filament\Forms\Components\TextInput;

class SectionType extends CustomLayoutType
{
    public function viewModes(): array {
        return [
            "default" => SectionTypeView::class
        ];
    }

    public static function getFieldIdentifier(): string {
        return "section";
    }
}
