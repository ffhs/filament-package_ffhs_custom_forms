<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldType\NestedLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Wizard;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;

class WizardNestTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {

        return Wizard::make()
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->skippable(FieldMapper::getOptionParameter($record,"skippable"))
            ->steps($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        $label = FieldMapper::getOptionParameter($record,"show_title")? FieldMapper::getLabelName($record):"";

        if($label != "") $output = Fieldset::make($label);
        else $output = Group::make();

        return $output
            ->columns(1)
            ->columnSpanFull()
            ->schema($parameter["rendered"]);
    }

}
