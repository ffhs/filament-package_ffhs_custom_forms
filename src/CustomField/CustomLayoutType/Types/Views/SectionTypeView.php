<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Filament\Forms\Components\Section;

class SectionTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {
        return Section::make(FormMapper::getOptionParameter($record,"show_title")? FormMapper::getToolTips($record):"")
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->aside(FormMapper::getOptionParameter($record,"aside"))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {
        return \Filament\Infolists\Components\Section::make(FormMapper::getToolTips($record)) ->schema($parameter["rendered"]);
    }

}
