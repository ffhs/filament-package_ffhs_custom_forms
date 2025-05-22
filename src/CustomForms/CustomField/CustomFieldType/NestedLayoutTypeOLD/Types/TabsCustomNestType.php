<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\Types;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\TabsNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowLabelOption;

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
