<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Wizard;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Tabs\Tab;
use ReflectionClass;

class WizardNestTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {

        return Wizard::make()
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->skippable(FormMapper::getOptionParameter($record,"skippable"))
            ->steps($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        $label = FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"";

        if($label != "") $output = Fieldset::make($label);
        else $output = Group::make();

        return $output
            ->columns(1)
            ->columnSpanFull()
            ->schema($parameter["rendered"]);
    }

}
