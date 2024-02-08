<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

trait HasCustomOptionInfoListView
{

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component {

        $textEntry = TextEntry::make($type::getIdentifyKey($record));
        $answerer =$type->answare($record);

        if(empty($answerer)) $state =  "";
        else if(is_array($answerer))
            $state =  $type->getAllCustomOptions($record)->filter(fn($value, $id) => in_array($id,$answerer));
        else {
            $state = $type->getAllCustomOptions($record)->filter(fn($value, $id) => $id == $answerer);
            $textEntry->color("info");
        }


        $textEntry
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->label($type::class::getLabelName($record). ":")
            ->columnSpanFull()
            ->inlineLabel()
            ->badge()
            ->state($state);

        return $textEntry;
    }

}
