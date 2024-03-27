<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views\DateTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Filament\Forms\Components\TextInput;

class DateType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;


    public static function getFieldIdentifier(): string {return "date";}

    public function viewModes(): array {
        return  [
            'default'  => DateTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-calendar3";
    }


    public function getExtraTypeOptions(): array {
        return [
            'column_span' => new ColumnSpanOption(),
            'format'=> new FastTypeOption("Y-m-d",
                TextInput::make("format")
                ->label("Format") //ToDo Translate
                ->placeholder("Y-m-d"),
            ),
            'in_line_label' => new InLineLabelOption(),
            'new_line_option' => new NewLineOption(),
        ];
    }


}
