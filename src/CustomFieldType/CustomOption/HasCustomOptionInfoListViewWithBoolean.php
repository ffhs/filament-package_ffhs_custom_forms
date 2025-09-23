<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Components\Component;

trait HasCustomOptionInfoListViewWithBoolean
{
    use HasCustomOptionInfoListView {
        HasCustomOptionInfoListView::getEntryComponent as getBasicInfolistComponent;
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        if (!$this->getOptionParameter($customFieldAnswer, 'boolean')) {
            return $this->getBasicInfolistComponent($customFieldAnswer, $parameter);
        }

        $answer = $this->getAnswer($customFieldAnswer);
        $answer = is_null($answer) ? false : $answer;

        return IconEntry::make($this->getIdentifyKey($customFieldAnswer))
            ->label($this->getLabelName($customFieldAnswer))
            ->state($answer)
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }
}
