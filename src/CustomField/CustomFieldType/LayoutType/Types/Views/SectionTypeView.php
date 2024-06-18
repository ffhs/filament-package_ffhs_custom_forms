<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;

class SectionTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {

        $label = FieldMapper::getOptionParameter($record,"show_title")? FieldMapper::getLabelName($record):"";

        return Section::make($label)
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->columns(FieldMapper::getOptionParameter($record,"columns"))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->aside(FieldMapper::getOptionParameter($record,"aside"))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(!FieldMapper::getOptionParameter($record,"show_in_view"))
            return Group::make($parameter["rendered"])->columnStart(1)->columnSpanFull();


        if(FieldMapper::getOptionParameter($record,"show_as_fieldset"))
            return Fieldset::make(FieldMapper::getLabelName($record))
                ->schema($parameter["rendered"])
                ->columnStart(1)
                ->columnSpanFull();

        return \Filament\Infolists\Components\Section::make(FieldMapper::getLabelName($record))
            ->schema($parameter["rendered"])
            ->columnStart(1)
            ->columnSpanFull();
    }

}
