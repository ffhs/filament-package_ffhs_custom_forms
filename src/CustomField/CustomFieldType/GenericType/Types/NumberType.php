<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidatioTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\NumberTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Toggle;

class NumberType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string {return "number";}

    public function viewModes(): array {
        return [
            'default' => NumberTypeView::class
        ];
    }

    public function icon(): string {
        return  "tabler-number";
    }

    public function extraTypeOptions(): array {
        return [
            DefaultLayoutTypeOptionGroup::make(),
            ValidatioTypeOptionGroup::make(typeOptions: [
                'min_value'=>new MinValueOption(),
                'max_value'=>new MaxValueOption(),
            ] )
        ];
    }


}
