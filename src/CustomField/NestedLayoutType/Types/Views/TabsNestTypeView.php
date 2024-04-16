<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;

class TabsNestTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {

        $label = FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"";

        return Tabs::make($label)
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->tabs($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        $label = FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"";
        return \Filament\Infolists\Components\Tabs::make($label)
            ->columnStart(1)
            ->tabs($parameter["rendered"])
            ->columnSpanFull();
    }

}
