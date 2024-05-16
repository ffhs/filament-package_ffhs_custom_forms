<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class SpaceTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Group {

        $spaces = [];

        for ($count = 0; $count < FormMapper::getOptionParameter($record,"amount"); $count+=1){
            $spaces[] = Placeholder::make(FormMapper::getIdentifyKey($record) . "-". $count)
                ->content("")
                ->label("")
                ->columnSpanFull();
        }

        return Group::make($spaces)
            ->columns(1)
            ->columnSpanFull();
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Group {

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return \Filament\Infolists\Components\Group::make();

        $spaces = [];

        for ($count = 0; $count < FormMapper::getOptionParameter($record,"amount"); $count+=1){
            $spaces[] = TextEntry::make(FormMapper::getIdentifyKey($record) . "-". $count)
                ->state(" ")
                ->label("");
        }
        return \Filament\Infolists\Components\Group::make($spaces)
            ->columns(1)
            ->columnSpanFull();
    }

}
