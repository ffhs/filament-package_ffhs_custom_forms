<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

trait HasCustomOptionInfoListView
{
    use CanMapFields;

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        $textEntry = TextEntry::make($this->getIdentifyKey($record));
        $answer = $this->getAnswer($record);
        $stateList = [];

        if (empty($answer)) {
            $answer = '';
        } elseif (is_array($answer)) {
            $stateList = $this
                ->getAllCustomOptions($record)
                ->filter(fn($value, $id) => in_array($id, $answer, false))
                ->toArray();
        } else {
            $stateList = $this
                ->getAllCustomOptions($record)
                ->firstWhere(fn($value, $id) => $id == $answer);
            $stateList = [$answer => $stateList];
            $textEntry->color('info');
        }

        $textEntry
            ->columnStart($this->getOptionParameter($record, 'new_line'))
            ->label($this->getLabelName($record))
            ->columnSpanFull()
            ->inlineLabel()
            ->state($this->getAnswer($record))
            ->formatStateUsing(fn($state) => $stateList[$state] ?? '')
            ->badge();

        return $textEntry;
    }
}
