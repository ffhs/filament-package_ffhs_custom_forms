<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutTypeLayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\AsideOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowLabelOption;
use Filament\Forms\Components\Field;

class SectionType extends CustomLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'section';
    }

    public function viewModes(): array
    {
        return [
            'default' => SectionTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'tabler-section';
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutTypeLayoutOptionGroup::make()
                ->mergeTypeOptions([
                    'show_in_view' => ShowInViewOption::make()
                        ->modifyOptionComponent(fn(Field $component) => $component->columnStart(1)),
                    'show_as_fieldset' => ShowAsFieldsetOption::make()
                        ->modifyOptionComponent(fn(Field $component) => $component->columnStart(2)),
                    'show_label' => ShowLabelOption::make(),
                    'aside' => AsideOption::make(),
                ]),
        ];
    }
}
