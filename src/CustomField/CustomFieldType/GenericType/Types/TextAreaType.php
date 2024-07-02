<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TextAreaTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Toggle;

class TextAreaType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string {return "textarea";}


    public function viewModes(): array {
        return  [
          'default'=>   TextAreaTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-textarea-t";
    }



    public function extraTypeOptions(): array {

        $autoSizeComponent =
            Toggle::make("auto_size")
            ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.auto_size"))
            ->columnSpan(2);

        $autoSize = FastTypeOption::makeFast(false, $autoSizeComponent);

        return [
            DefaultLayoutTypeOptionGroup::make()->addTypeOptions("auto_size", $autoSize),
            ValidationTypeOptionGroup::make(typeOptions: [
                'max_length' => new MaxLengthOption(),
                'min_length' => new MinLengthOption(),
            ] )
        ];
    }


}
