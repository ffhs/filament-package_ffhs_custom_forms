<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\EggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\NestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views\TabsNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;

class TabsNestType extends NestLayoutType
{
    use HasBasicSettings;
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
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

    public function getEggType(): EggLayoutType {
        return new TabEggType();
    }
}
