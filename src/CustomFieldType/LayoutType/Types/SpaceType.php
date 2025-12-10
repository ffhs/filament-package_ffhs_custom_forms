<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\SpaceTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\AmountOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;

class SpaceType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'space';
    protected static string $icon = 'carbon-name-space';
    protected static array $viewModes = [
        'default' => SpaceTypeView::class,
    ];

    public function hasEditorNameElement(array $fielData): bool
    {
        return false;
    }

    public function isFullSizeField(): bool
    {
        return true;
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->removeTypeOption('new_line')
                ->setTypeOptions([
                    'amount' => AmountOption::make(),
                    'show_in_view' => ShowInViewOption::make(),
                ]),
        ];
    }
}
