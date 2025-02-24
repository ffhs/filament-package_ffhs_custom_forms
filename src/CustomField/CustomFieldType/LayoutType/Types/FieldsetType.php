<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\FieldsetTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutTypeDefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;

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
            LayoutTypeDefaultLayoutTypeOptionGroup::make()
                ->removeTypeOption("helper_text")
                ->mergeTypeOptions([
                    "show_title" => ShowTitleOption::make(),
                    'show_in_view' => ShowInViewOption::make(),
                ]),
        ];
    }

}
