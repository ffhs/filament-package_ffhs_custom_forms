<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

trait HasCustomOptionInfoListView
{

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): Component {

        $textEntry = TextEntry::make(FieldMapper::getIdentifyKey($record));
        $answer = FieldMapper::getAnswer($record);

        if(empty($answer)) $answer =  "";
        else if(is_array($answer))
            $stateList = FieldMapper::getAllCustomOptions($record)->filter(fn($value, $id) => in_array($id,$answer));
        else {
            $stateList = FieldMapper::getAllCustomOptions($record)->filter(fn($value, $id) => $id == $answer);
            $textEntry->color("info");
        }


        $textEntry
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label(FieldMapper::getLabelName($record))
            ->columnSpanFull()
            ->inlineLabel()
            ->state(FieldMapper::getAnswer($record))
            ->formatStateUsing(fn($state) => $stateList->toArray()[$state])
            ->badge();



        return $textEntry;
    }

}
