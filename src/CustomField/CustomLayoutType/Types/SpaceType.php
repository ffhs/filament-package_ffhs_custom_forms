<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\SpaceTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\TextInput;

class SpaceType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
        return "space";
    }

    public function viewModes(): array {
        return [
            "default" => SpaceTypeView::class
        ];
    }

    public function icon(): string {
        return  "carbon-name-space";
    }

    public function hasToolTips(): bool {
        return false;
    }


    public function getExtraTypeOptions(): array {
        return[
            'amount'=> new FastTypeOption(1,
                TextInput::make("amount")
                    ->label("Grösse")
                    ->minValue(1)
                    ->required()
                    ->numeric()
            ),
            'show_in_view'=> new ShowInViewOption(),
        ];
    }

}
