<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\ColorPickerTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Select;

class ColorPickerType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'color_input';
    }

    public function viewModes(): array
    {
        return [
            'default' => ColorPickerTypeView::class,
        ];
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'color_type' => new FastTypeOption(
                        'rgb',
                        Select::make('color_type')
                            ->label(TypeOption::__('color_type.label'))
                            ->helperText(TypeOption::__('color_type.helper_text'))
                            ->columnSpanFull()
                            ->required()
                            ->selectablePlaceholder(false)
                            ->options([
                                'rgb' => 'RGB',
                                'hsl' => 'HSL',
                                'rgba' => 'RGBA',
                            ])
                    ),
                ]),
        ];
    }


    public function icon(): string
    {
        return 'carbon-color-palette';
    }

}
