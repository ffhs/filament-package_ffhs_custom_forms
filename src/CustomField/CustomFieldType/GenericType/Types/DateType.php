<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\DateTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\TextInput;

class DateType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;


    public static function identifier(): string {return "date";}

    public function viewModes(): array {
        return  [
            'default'  => DateTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-calendar3";
    }


    public function extraTypeOptions(): array {
        return [
            'column_span' => new ColumnSpanOption(),
            'format'=> new FastTypeOption("Y-m-d",
                TextInput::make("format")
                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.format"))
                ->placeholder("Y-m-d"),
            ),
            'in_line_label' => new InLineLabelOption(),
            'new_line_option' => new NewLineOption(),
        ];
    }


}
