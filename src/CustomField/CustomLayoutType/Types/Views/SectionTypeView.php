<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;

class SectionTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {

        $label = FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"";

        return Section::make($label)
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->aside(FormMapper::getOptionParameter($record,"aside"))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return Group::make($parameter["rendered"])->columnStart(1)->columnSpanFull();


        if(FormMapper::getOptionParameter($record,"show_as_fieldset"))
            return Fieldset::make(FormMapper::getLabelName($record))
                ->schema($parameter["rendered"])
                ->columnStart(1)
                ->columnSpanFull();

        return \Filament\Infolists\Components\Section::make(FormMapper::getLabelName($record))
            ->schema($parameter["rendered"])
            ->columnStart(1)
            ->columnSpanFull();
    }

}
