<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views\NumberTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinValueOption;

class NumberType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "number";}

    public function viewModes(): array {
        return [
            'default' => NumberTypeView::class
        ];
    }

    public function icon(): string {
        return  "tabler-number";
    }

    protected function extraOptionsAfterBasic(): array {

        return [
            'min_value'=>new MinValueOption(),
            'max_value'=>new MaxValueOption(),
        ];
    }






}
