<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\GroupTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutTypeLayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;

class GroupType extends CustomLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'group';
    }

    public function viewModes(): array
    {
        return [
            'default' => GroupTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'carbon-cics-system-group';
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutTypeLayoutOptionGroup::make()
                ->removeTypeOption('helper_text')
                ->mergeTypeOptions([
                    'show_in_view' => ShowInViewOption::make(),
                ]),
        ];
    }
}
