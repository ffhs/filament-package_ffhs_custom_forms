<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\TextLayoutTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\TextOption;

class TextLayoutType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'layout_text';
    protected static string $icon = 'heroicon-m-chat-bubble-bottom-center-text';
    protected static array $viewModes = [
        'default' => TextLayoutTypeView::class,
    ];

    public function extraTypeOptions(): array
    {

        return [
            LayoutOptionGroup::make()
                ->addTypeOptions('show_in_view', ShowInViewOption::make())
                ->removeTypeOption('helper_text')
                ->addTypeOptions('text', TextOption::make()),
        ];
    }
}
