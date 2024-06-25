<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\WizardNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Filament\Forms\Components\Toggle;

class WizardCustomNestType extends CustomNestLayoutType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string {
        return "wizard";
    }

    public function viewModes(): array {
        return  [
          'default'=> WizardNestTypeView::class,
        ];
    }

    public function extraTypeOptions(): array{
        return [
            'column_span' => new ColumnSpanOption(),
            'show_title' => (new ShowTitleOption())->modifyComponent(
                fn (Toggle $toggle) => $toggle
                    ->label("Zeige den Title während dem Betrachten an")
                    ->columnSpanFull()
            ),
            'new_line_option' => new NewLineOption(),
            'skippable' => new FastTypeOption(false,
                Toggle::make("skippable")
                    ->label("Die Schritte können übersprungen werden")
                    ->afterStateUpdated(fn($set, bool $state)=> $state?null: $set("has_continue_action", true))
                    ->columnSpanFull()
                    ->live()
            ),
        ];
    }


    public function icon(): string {
       return "carbon-connect";
    }

    public function getEggType(): CustomEggLayoutType {
        return new WizardStepCustomEggType();
    }
}
