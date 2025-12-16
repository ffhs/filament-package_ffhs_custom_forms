<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\TextAreaTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;

class TextAreaType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'textarea';
    protected static string $icon = 'bi-textarea-t';
    protected static array $viewModes = [
        'default' => TextAreaTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->addTypeOptions(
                    'auto_size',
                    FastTypeOption::makeFast(
                        false,
                        static fn($name) => Toggle::make($name)
                            ->helperText(TypeOption::__('auto_size.helper_text'))
                            ->label(TypeOption::__('auto_size.label'))
                            ->columnSpan(2)
                    )
                ),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'max_length' => MaxLengthOption::make(),
                    'min_length' => MinLengthOption::make(),
                ]),
        ];
    }
}
