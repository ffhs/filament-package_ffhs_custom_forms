<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\CheckboxListTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;

class CheckboxListType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings {
        HasBasicSettings::extraTypeOptions as getExtraSettingsOptions;
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

    public function extraTypeOptions(): array {
        return array_merge(
            ["columns" => new ColumnsOption()],
            $this->getExtraSettingsOptions(),
            parent::extraTypeOptions()
        );
    }



}
