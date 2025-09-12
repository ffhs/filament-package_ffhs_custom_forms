<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Ffhs\FfhsUtils\Filament\DragDrop\DragDropGroup;
use Ffhs\FfhsUtils\Traits\CanLoadDefaultFromComponents;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;

class EventComponent extends DragDropGroup
{
    use HasRuleEvents;
    use CanLoadDefaultFromComponents;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Ausführung'); //ToDo Translate
        $this->itemLabel(fn($itemState) => empty($itemState['type']) ? '' : $this
            ->getEvent($itemState['type'])
            ->getDisplayName()
        );

        $this->schema([
            Select::make('type')
                ->options($this->getEventOptions(...))
                ->selectablePlaceholder()
                ->nullable(false)
                ->hiddenLabel()
                ->required()
                ->live()
                ->afterStateUpdated(function ($set, $state) {
                    $components = $this->getEvent($state)?->getFormSchema() ?? [];
                    $default = $this->loadDefaultFromComponents($components, null);
                    $set('data', $default);
                }),

            Group::make()
                ->schema(fn($get) => $this->getEvent($get('type'))?->getFormSchema() ?? [])
                ->statePath('data')
                ->live()
        ]);
    }

    protected function getEventOptions(): array
    {
        return collect($this->getEvents())
            ->map(fn(EventType $events) => [
                'identifier' => $events::identifier(),
                'label' => $events->getDisplayName(),
            ])
            ->pluck('label', 'identifier')
            ->toArray();
    }


}
