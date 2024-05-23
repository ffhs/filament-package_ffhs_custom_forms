<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\IconEntry;

trait HasCustomOptionInfoListViewWithBoolean
{
    use HasCustomOptionInfoListView{
        HasCustomOptionInfoListView::getInfolistComponent as getBasicInfolistComponent;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component {

        if(!FormMapper::getOptionParameter($record, "boolean"))
            return self::getBasicInfolistComponent($type,$record,$parameter);

        $answer = FormMapper::getAnswer($record);
        $answer = is_null($answer)? false: $answer;

        return IconEntry::make(FormMapper::getIdentifyKey($record))
            ->label(FormMapper::getLabelName($record). ":")
            ->state(FormMapper::getAnswer($answer))
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }

}
