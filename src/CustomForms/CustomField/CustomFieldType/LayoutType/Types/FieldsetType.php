<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\FieldsetTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutTypeLayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowLabelOption;

class FieldsetType extends CustomLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "fieldset";
    }

    public function viewModes(): array
    {
        return [
            "default" => FieldsetTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-columns-gap";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutTypeLayoutOptionGroup::make()
                ->removeTypeOption("helper_text")
                ->mergeTypeOptions([
                    "show_label" => ShowLabelOption::make(),
                    'show_in_view' => ShowInViewOption::make(),
                ]),
        ];
    }

}
