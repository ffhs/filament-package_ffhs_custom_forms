<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\Fieldset;

class GroupTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {

        return Group::make()
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(FormMapper::getOptionParameter($record,"show_in_view"))
            return Fieldset::make(FormMapper::getLabelName($record))
                ->schema($parameter["rendered"])
                ->columnStart(1)
                ->columnSpanFull();

        return \Filament\Infolists\Components\Group::make($parameter["rendered"])
            ->columnStart(1)
            ->columnSpanFull();
    }

}
