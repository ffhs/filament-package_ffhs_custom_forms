<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldType\NestedLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Tabs;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Tabs\Tab;
use ReflectionClass;

class TabsNestTypeView implements FieldTypeView
{

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        $label = FieldMapper::getOptionParameter($record, "show_label") ? FieldMapper::getLabelName($record) : "";

        return Tabs::make($label)
            ->columnSpan(FieldMapper::getOptionParameter($record, "column_span"))
            ->inlineLabel(FieldMapper::getOptionParameter($record, "in_line_label"))
            ->columnStart(FieldMapper::getOptionParameter($record, "new_line"))
            ->tabs($parameter["rendered"]);
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $label = FieldMapper::getOptionParameter($record, "show_label") ? FieldMapper::getLabelName($record) : "";

        if (!FieldMapper::getOptionParameter($record, "show_as_fieldset")) {
            return \Filament\Infolists\Components\Tabs::make($label)
                ->columnStart(1)
                ->tabs($parameter["rendered"])
                ->columnSpanFull();
        }

        $schema = [];
        $tabs = $parameter["rendered"];

        $reflection = new ReflectionClass(Tab::class);
        $propertyLabel = $reflection->getProperty("label");
        $propertyChildComponents = $reflection->getProperty("childComponents");

        foreach ($tabs as $tab) {
            /**@var Tab $tab */
            $schema[] = Fieldset::make($propertyLabel->getValue($tab))
                ->schema($propertyChildComponents->getValue($tab));
        }

        if ($label == "") {
            $output = Group::make();
        } else $output = Fieldset::make($label);

        return $output
            ->columns(1)
            ->columnSpanFull()
            ->schema($schema);
    }

}
