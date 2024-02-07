<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\DateTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Filament\Forms\Components\TextInput;

class DateType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "date";}

    public function getExtraOptionSchema(bool $isInheritGeneral = false): ?array {
        return [
            $this->getColumnSpanOption(),
            TextInput::make("format")
                ->label("Format") //ToDo Translate
                ->placeholder("Y-m-d"),
            $this->getNewLineOption(),
            $this->getInLineLabelOption()

        ];
    }

    public function getExtraOptionFields(bool $isInheritGeneral = false): array {
        return [
            'format'=>null,
            'column_span' => 3,
            'in_line_label' => false,
            'new_line_option' => true,
        ];
    }

    public function viewModes(): array {
        return  [
            'default'  => DateTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-calendar3";
    }
}
