<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\DateTimeTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\TextInput;

class DateTimeType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "date-time";}


    public function getExtraOptionSchema(?GeneralField $generalField = null): ?array {
        return [
            $this->getColumnSpanOption(),
            TextInput::make("format")
                ->label("Format") //ToDo Translate
                ->placeholder("Y-m-d H:i:s")
                ->columnSpan(1),
            $this->getNewLineOption(),
            $this->getInLineLabelOption()

        ];
    }
    public function getExtraOptionFields(?GeneralField $generalField = null): array {
        return [
            'format'=>null,
            'column_span' => 3,
            'in_line_label' => false,
            'new_line_option' => true,
        ];
    }

    public function viewModes(): array {
        return  [
            'default'  => DateTimeTypeView::class
        ];
    }

    public function icon(): string {
        return  "heroicon-s-clock";
    }
}
