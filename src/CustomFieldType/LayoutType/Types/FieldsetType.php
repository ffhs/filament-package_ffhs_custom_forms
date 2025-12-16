<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\FieldsetTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutTypeLayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowLabelOption;

class FieldsetType extends CustomLayoutType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'fieldset';
    protected static string $icon = 'bi-columns-gap';
    protected static array $viewModes = [
        'default' => FieldsetTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutTypeLayoutOptionGroup::make()
                ->removeTypeOption('helper_text')
                ->mergeTypeOptions([
                    'show_label' => ShowLabelOption::make(),
                    'show_in_view' => ShowInViewOption::make(),
                ]),
        ];
    }

}
