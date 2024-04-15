<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLenghtOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLenghtOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class TagsType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;
    public static function getFieldIdentifier(): string { return "tags_input"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\TagsTypeView::class,
        ];
    }

    public function icon(): String {
        return  "bi-tags";
    }

}
