<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Group as FormsGroup;
use Filament\Infolists\Components\Group as InfolistsGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;

class SpaceTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $spaces = [];

        for ($count = 0; $count < $this->getOptionParameter($customField, 'amount'); $count++) {
            $spaces[] = TextEntry::make($this->getIdentifyKey($customField) . '-' . $count)
                ->columnSpanFull()
                ->hiddenLabel()
                ->state(' ');
        }

        return $this
            ->modifyComponent(FormsGroup::make($spaces), $customField) //ToDo ähhh
            ->columns(1)
            ->columnSpanFull();
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        if (!$this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            return Group::make()->hidden();
        }

        $spaces = [];

        for ($count = 0; $count < $this->getOptionParameter($customFieldAnswer, 'amount'); $count++) {
            $spaces[] = TextEntry::make($this->getIdentifyKey($customFieldAnswer) . '-' . $count)
                ->state(' ')
                ->label('');
        }

        return $this
            ->modifyComponent(Group::make($spaces), $customFieldAnswer, true)
            ->columns(1)
            ->columnSpanFull();
    }
}
