<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;

class SpaceTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $spaces = $this->createSpaceEntries($customField);

        return $this
            ->modifyComponent(Group::make($spaces), $customField, false)
            ->columns(1)
            ->columnSpanFull();
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        if (!$this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            return Group::make()->hidden();
        }

        $spaces = $this->createSpaceEntries($customFieldAnswer);

        return $this
            ->modifyComponent(Group::make($spaces), $customFieldAnswer, false)
            ->columns(1)
            ->columnSpanFull();
    }

    private function createSpaceEntries($object): array
    {
        $spaces = [];
        $amount = $this->getOptionParameter($object, 'amount');

        for ($count = 0; $count < $amount; $count++) {
            $spaces[] = $entry = TextEntry::make($this->getIdentifyKey($object) . '-' . $count)
                ->state(' ')
                ->columnSpanFull()
                ->hiddenLabel();
        }

        return $spaces;
    }

}
