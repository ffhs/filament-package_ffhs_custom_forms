<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Group;

class FieldsetTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {
        $label = FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"";

        return Fieldset::make($label)
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return Group::make($parameter["rendered"])->columnStart(1)->columnSpanFull();

        return \Filament\Infolists\Components\Fieldset::make(FormMapper::getLabelName($record))
            ->schema($parameter["rendered"])
            ->columnStart(1)
            ->columnSpanFull();
    }

}
