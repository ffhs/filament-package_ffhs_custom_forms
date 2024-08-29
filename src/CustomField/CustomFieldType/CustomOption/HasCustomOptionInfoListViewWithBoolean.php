<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\IconEntry;

trait HasCustomOptionInfoListViewWithBoolean
{
    use HasCustomOptionInfoListView{
        HasCustomOptionInfoListView::getInfolistComponent as getBasicInfolistComponent;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): Component {

        if(!FieldMapper::getOptionParameter($record, "boolean"))
            return self::getBasicInfolistComponent($type,$record,$parameter);

        $answer = FieldMapper::getAnswer($record);
        $answer = is_null($answer)? false: $answer;

        return IconEntry::make(FieldMapper::getIdentifyKey($record))
            ->label(FieldMapper::getLabelName($record))
            ->state($answer)
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }

}
