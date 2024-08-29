<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\DateTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;
use Filament\Forms\Components\TextInput;

class DateType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;


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
            ValidationTypeOptionGroup::make(typeOptions: [
                'required' => RequiredOption::make(),
                'format'=> FastTypeOption::makeFast("Y-m-d",
                    TextInput::make("format")
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.format"))
                        ->placeholder("Y-m-d"),
                ),
            ] )
        ];
    }


}
