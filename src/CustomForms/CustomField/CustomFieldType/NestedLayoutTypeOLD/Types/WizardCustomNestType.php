<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\WizardNestTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowLabelOption;
use Filament\Forms\Components\Toggle;

class WizardCustomNestType extends CustomNestLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "wizard";
    }

    public function viewModes(): array
    {
        return [
            'default' => WizardNestTypeView::class,
        ];
    }

    public function extraTypeOptions(): array
    {
        return [
            'column_span' => new ColumnSpanOption(),
            'show_label' => (ShowLabelOption::make())->modifyOptionComponent(
                fn(Toggle $toggle) => $toggle
                    ->label("Zeige den Title während dem Betrachten an")
                    ->columnSpanFull()
            ),
            'new_line' => NewLineOption::make(),
            'skippable' => new FastTypeOption(
                false,
                Toggle::make("skippable")
                    ->label("Die Schritte können übersprungen werden")
                    ->afterStateUpdated(fn($set, bool $state) => $state ? null : $set("has_continue_action", true))
                    ->columnSpanFull()
                    ->live()
            ),
        ];
    }


    public function icon(): string
    {
        return "carbon-connect";
    }

    public function getEggType(): CustomEggLayoutType
    {
        return new WizardStepCustomEggType();
    }
}
