<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\DateTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidatioTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinValueOption;
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
            DefaultLayoutTypeOptionGroup::make(),
            ValidatioTypeOptionGroup::make(typeOptions: [
                'format'=> FastTypeOption::makeFast("Y-m-d",
                    TextInput::make("format")
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.format"))
                        ->placeholder("Y-m-d"),
                ),
            ] )
        ];
    }


}
