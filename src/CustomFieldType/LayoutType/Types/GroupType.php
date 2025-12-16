<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\GroupTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutTypeLayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;

class GroupType extends CustomLayoutType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'group';
    protected static string $icon = 'carbon-cics-system-group';
    protected static array $viewModes = [
        'default' => GroupTypeView::class,
    ];

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
