<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\SuggestionsOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;

class TextType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'text';
    protected static string $icon = 'bi-input-cursor-text';
    protected static array $viewModes = [
        'default' => TextTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'alpine_mask' => AlpineMaskOption::make(),
                    'max_length' => MaxLengthOption::make(),
                    'min_length' => MinLengthOption::make(),
                ]),

            TypeOptionGroup::make(TypeOption::__('suggestions.label'), [
                'suggestions' => SuggestionsOption::make(),
            ]),
        ];
    }
}
