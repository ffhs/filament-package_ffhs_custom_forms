<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views\TabsNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views\WizardNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Filament\Forms\Components\Toggle;

class WizardCustomNestType extends CustomNestLayoutType
{
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
        return "wizard";
    }

    public function viewModes(): array {
        return  [
          'default'=> WizardNestTypeView::class,
        ];
    }

    public function getExtraTypeOptions(): array{
        return [
            'column_span' => new ColumnSpanOption(),
            'show_title' => (new ShowTitleOption())->modifyComponent(
                fn (Toggle $toggle) => $toggle
                    ->label("Zeige den Title während dem Betrachten an")
                    ->columnSpanFull()
            ),
            'new_line_option' => new NewLineOption(),
        ];
    }


    public function icon(): string {
       return "carbon-connect";
    }

    public function getEggType(): CustomEggLayoutType {
        return new WizardStepCustomEggType();
    }
}
