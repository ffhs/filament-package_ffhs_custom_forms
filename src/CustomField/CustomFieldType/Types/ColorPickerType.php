<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Filament\Forms\Components\Select;

class ColorPickerType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;
    public static function getFieldIdentifier(): string { return "color_input"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\ColorPickerTypeView::class,
        ];
    }

    protected function extraOptionsAfterBasic(): array {
        return [
            'color_type' => new FastTypeOption("rgb",
                Select::make("color_type")
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.color_type"))
                    ->columnSpanFull()
                    ->required()
                    ->selectablePlaceholder(false)
                    ->options([
                        "rgb" => "RGB",
                        "hsl" => "HSL",
                        "rgba" => "RGBA",
                    ])
            ),
        ];
    }


    public function icon(): String {
        return  "carbon-color-palette";
    }

}
