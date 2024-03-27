<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;

class CheckboxListType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings {
        HasBasicSettings::getExtraTypeOptions as getExtraSettingsOptions;
    }

    public static function getFieldIdentifier(): string { return "checkbox_list"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\CheckboxListTypeView::class,
        ];
    }
    public function icon(): String {
        return  "bi-ui-checks-grid";
    }

    public function getExtraTypeOptions(): array {
        return array_merge(
            ["columns" => new ColumnsOption()],
            $this->getExtraSettingsOptions(),
            parent::getExtraTypeOptions()
        );
    }



}
