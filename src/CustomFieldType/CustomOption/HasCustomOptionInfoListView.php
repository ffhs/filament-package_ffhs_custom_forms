<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;

trait HasCustomOptionInfoListView
{
    use CanMapFields;

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $textEntry = TextEntry::make($this->getIdentifyKey($customFieldAnswer));
        $answer = $this->getAnswer($customFieldAnswer);
        $stateList = [];

        if (empty($answer)) {
            $answer = '';
        } elseif (is_array($answer)) {
            $stateList = $this
                ->getAllCustomOptions($customFieldAnswer)
                ->filter(fn($value, $id) => in_array($id, $answer, false))
                ->toArray();
        } else {
            $stateList = $this
                ->getAllCustomOptions($customFieldAnswer)
                ->firstWhere(fn($value, $id) => $id == $answer);
            $stateList = [$answer => $stateList];
            $textEntry->color('info');
        }

        $textEntry
            ->columnStart($this->getOptionParameter($customFieldAnswer, 'new_line'))
            ->label($this->getLabelName($customFieldAnswer))
            ->columnSpanFull()
            ->inlineLabel()
            ->state($this->getAnswer($customFieldAnswer))
            ->formatStateUsing(fn($state) => $stateList[$state] ?? '')
            ->badge();

        return $textEntry;
    }
}
