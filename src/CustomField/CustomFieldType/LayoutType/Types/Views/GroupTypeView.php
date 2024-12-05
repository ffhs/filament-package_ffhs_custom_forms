<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\Fieldset;

class GroupTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component {

        return Group::make()
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->columns(FieldMapper::getOptionParameter($record,"columns"))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->schema($parameter["renderer"]());
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): \Filament\Infolists\Components\Component {

        if(FieldMapper::getOptionParameter($record,"show_in_view"))
            return Fieldset::make(FieldMapper::getLabelName($record))
                ->schema($parameter["renderer"]())
                ->columnStart(1)
                ->columnSpanFull();

        return \Filament\Infolists\Components\Group::make($parameter["rendered"])
            ->columnStart(1)
            ->columnSpanFull();
    }

}
