<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\CheckboxListTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;

class CheckboxListType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings {
        HasBasicSettings::getExtraTypeOptions as getExtraSettingsOptions;
    }

    public static function identifier(): string { return "checkbox_list"; }

    public function viewModes(): array {
        return  [
            'default'  => CheckboxListTypeView::class,
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
