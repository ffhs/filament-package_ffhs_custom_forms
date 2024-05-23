<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

trait HasCustomOptionInfoListView
{

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component {

        $textEntry = TextEntry::make(FormMapper::getIdentifyKey($record));
        $answer = FormMapper::getAnswer($record);

        if(empty($answer)) $state =  "";
        else if(is_array($answer))
            $stateList = FormMapper::getAllCustomOptions($record)->filter(fn($value, $id) => in_array($id,$answer));
        else {
            $stateList = FormMapper::getAllCustomOptions($record)->filter(fn($value, $id) => $id == $answer);
            $textEntry->color("info");
        }


        $textEntry
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->label(FormMapper::getLabelName($record). ":")
            ->columnSpanFull()
            ->inlineLabel()
            ->state(FormMapper::getAnswer($record))
            ->formatStateUsing(fn($state) => $stateList->toArray()[$state])
            ->badge();



        return $textEntry;
    }

}
