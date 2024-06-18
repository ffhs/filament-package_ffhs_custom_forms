<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\RadioTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Component;

class RadioType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings {
        HasBasicSettings::getExtraTypeOptions as getExtraSettingsOptions;
    }

    public static function identifier(): string { return "radio"; }

    public function viewModes(): array {
        return  [
            'default'  => RadioTypeView::class,
        ];
    }

    public function getExtraTypeOptions(): array {
        return array_merge(
            $this->getExtraSettingsOptions(),
            [
               "boolean" => new BooleanOption(),
               "inline" => new InlineOption(),
               parent::getExtraTypeOptions()["customOptions" ]
                   ->modifyComponent(fn(Component $component) => $component->hidden(fn($get) => $get("boolean")))
            ],
        );
    }


    public function icon(): String {
        return  "carbon-radio-button-checked";
    }
}
