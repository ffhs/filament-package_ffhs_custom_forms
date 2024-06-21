<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\TabsNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;

class TabsCustomNestType extends CustomNestLayoutType
{
    use HasBasicSettings;
    use HasCustomFormPackageTranslation;

    public static function identifier(): string {
        return "tabs";
    }

    public function viewModes(): array {
        return  [
          'default'=> TabsNestTypeView::class,
        ];
    }

    protected function extraOptionsAfterBasic(): array {
        return [
          'show_title' => new ShowTitleOption(),
          'show_as_fieldset' => new ShowAsFieldsetOption()
        ];
    }


    public function icon(): string {
       return "carbon-new-tab";
    }

    public function getEggType(): CustomEggLayoutType {
        return new CustomTabCustomEggType();
    }
}
