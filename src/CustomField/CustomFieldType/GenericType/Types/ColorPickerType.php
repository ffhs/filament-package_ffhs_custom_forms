<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\ColorPickerTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Filament\Forms\Components\Select;

class ColorPickerType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "color_input";
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
                        "rgb",
                        Select::make("color_type")
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.color_type")
                            )
                            ->columnSpanFull()
                            ->required()
                            ->selectablePlaceholder(false)
                            ->options([
                                "rgb" => "RGB",
                                "hsl" => "HSL",
                                "rgba" => "RGBA",
                            ])
                    ),
                ]),
        ];
    }


    public function icon(): string
    {
        return "carbon-color-palette";
    }

}
