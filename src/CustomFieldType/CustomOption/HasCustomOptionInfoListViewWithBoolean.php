<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\IconEntry;

trait HasCustomOptionInfoListViewWithBoolean
{
    use HasCustomOptionInfoListView {
        HasCustomOptionInfoListView::getInfolistComponent as getBasicInfolistComponent;
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!$this->getOptionParameter($record, 'boolean')) {
            return $this->getBasicInfolistComponent($type, $record, $parameter);
        }

        $answer = $this->getAnswer($record);
        $answer = is_null($answer) ? false : $answer;

        return IconEntry::make($this->getIdentifyKey($record))
            ->label($this->getLabelName($record))
            ->state($answer)
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }
}
