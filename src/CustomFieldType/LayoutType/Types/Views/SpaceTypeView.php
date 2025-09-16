<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Group as FormsGroup;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Group as InfolistsGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;

class SpaceTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        $spaces = [];

        for ($count = 0; $count < $this->getOptionParameter($record, 'amount'); $count++) {
            $spaces[] = Placeholder::make($this->getIdentifyKey($record) . '-' . $count)
                ->content('')
                ->label('')
                ->columnSpanFull();
        }

        return $this
            ->modifyFormComponent(FormsGroup::make($spaces), $record)
            ->columns(1)
            ->columnSpanFull();
    }

    public function getEntryComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return InfolistsGroup::make()
                ->hidden();
        }

        $spaces = [];

        for ($count = 0; $count < $this->getOptionParameter($record, 'amount'); $count++) {
            $spaces[] = TextEntry::make($this->getIdentifyKey($record) . '-' . $count)
                ->state(' ')
                ->label('');
        }

        return $this
            ->modifyInfolistComponent(InfolistsGroup::make($spaces), $record)
            ->columns(1)
            ->columnSpanFull();
    }
}
