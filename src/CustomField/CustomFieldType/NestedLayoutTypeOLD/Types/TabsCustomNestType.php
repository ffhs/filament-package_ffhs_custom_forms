<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\TabsNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowLabelOption;

class TabsCustomNestType extends CustomNestLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "tabs";
    }

    public function viewModes(): array
    {
        return [
            'default' => TabsNestTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "carbon-new-tab";
    }

    public function getEggType(): CustomEggLayoutType
    {
        return new CustomTabCustomEggType();
    }

    protected function extraOptionsAfterBasic(): array
    {
        return [
            'show_label' => ShowLabelOption::make(),
            'show_as_fieldset' => new ShowAsFieldsetOption(),
        ];
    }
}
