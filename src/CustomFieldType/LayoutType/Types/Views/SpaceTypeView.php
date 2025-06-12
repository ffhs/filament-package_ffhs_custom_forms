<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;

class SpaceTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        $spaces = [];

        for ($count = 0; $count < $this->getOptionParameter($record, 'amount'); $count += 1) {
            $spaces[] = Placeholder::make($this->getIdentifyKey($record) . '-' . $count)
                ->content('')
                ->label('')
                ->columnSpanFull();
        }

        return $this->modifyFormComponent(Group::make($spaces), $record)
            ->columns(1)
            ->columnSpanFull();
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return \Filament\Infolists\Components\Group::make()->hidden();
        }

        $spaces = [];

        for ($count = 0; $count < $this->getOptionParameter($record, 'amount'); $count += 1) {
            $spaces[] = TextEntry::make($this->getIdentifyKey($record) . '-' . $count)
                ->state(' ')
                ->label('');
        }
        return $this->modifyInfolistComponent(\Filament\Infolists\Components\Group::make($spaces), $record)
            ->columns(1)
            ->columnSpanFull();
    }
}
