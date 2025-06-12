<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;


class RuleEditor extends Group
{

    protected array|Closure|null $triggers;
    protected Closure|array|null $events;

    //  ToDo check if fill bug if fixable with    $component->getChildComponentContainer($newUuid ?? array_key_last($items))->fill();

    public function triggers(array|Closure|null $triggers): static
    {
        $this->triggers = $triggers;
        return $this;
    }

    public function events(array|Closure|null $events): static
    {
        $this->events = $events;
        return $this;
    }

    public function getTrigger($type): TriggerType
    {
        return collect($this->getTriggers())->filter(fn(TriggerType $event) => $event::identifier() === $type)->first();
    }

    /**
     * @return TriggerType[]
     */
    public function getTriggers(): array
    {
        $types = $this->evaluate($this->triggers) ?? [];

        if (empty($types)) {
            return [];
        }

        if ($types[0] instanceof TriggerType) {
            return $types;
        }

        $finalTypes = [];
        foreach ($types as $typeClass) {
            $finalTypes[] = $typeClass::make();
        }

        return $finalTypes;
    }

    public function getEventDropComponent(): DragDropComponent
    {
        return DragDropComponent::make('events')
            ->label('Ausführung')
            // ->dragDropGroup('events')
            ->dragDropGroup(fn() => uniqid())
            ->deepColor(1)
            ->orderAttribute('order')
            ->itemLabel(fn($itemState
            ) => empty($itemState['type']) ? '' : $this->getEvent($itemState['type'])->getDisplayName())
            ->itemActions(fn() => [
                $this->getRemoveAction(),
            ])
            ->schema([
                Select::make('type')
                    ->label('')
                    ->required()
                    ->selectablePlaceholder()
                    ->nullable(false)
                    ->options($this->getEventOptions(...))
                    ->afterStateUpdated(function ($set) {
                        $set('data', []);
                    })
                    ->live(),

                Group::make()
                    ->statePath('data')
                    ->schema(function ($get) {
                        if (empty($get('type'))) {
                            return [];
                        }
                        $trigger = collect($this->getEvents())
                            ->filter(fn(EventType $event) => $event::identifier() === $get('type'))
                            ->first();
                        if (is_null($trigger)) {
                            return [];
                        }
                        /**@var EventType $trigger */
                        return $trigger->getFormSchema();
                    })
                    ->live()
            ]);
    }

    public function getEvent($type): EventType
    {
        return collect($this->getEvents())->filter(fn(EventType $event) => $event::identifier() === $type)->first();
    }

    public function getEvents(): array
    {
        $types = $this->evaluate($this->events) ?? [];

        if (empty($types)) {
            return [];
        }

        if ($types[0] instanceof EventType) {
            return $types;
        }

        $finalTypes = [];
        foreach ($types as $typeClass) {
            $finalTypes[] = $typeClass::make();
        }

        return $finalTypes;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->triggers(config('ffhs_custom_forms.rule.trigger'));
        $this->events(config('ffhs_custom_forms.rule.event'));

        $this->schema([
            Action::make('AddRule')
                ->icon('carbon-rule')
                ->label('Regel Hinzufügen')  //ToDo translate
                ->action(function ($set, $get) {
                    $rules = $get('rules');
                    $rules[uniqid()] = [
                        'is_oder_mode' => false,
                    ];
                    $set('rules', $rules);
                })
                ->toFormComponent(),
            DragDropComponent::make('rules')
                ->label('')
                ->itemIcons('carbon-rule')
                ->itemLabel(function ($item, $state) {

                    $triggers = sizeof($state[$item]['triggers'] ?? []);
                    $event = sizeof($state[$item]['events'] ?? []);

                    return 'Regel (' . $triggers . 'T : ' . $event . 'E)'; //ToDo Translate
                })
                ->dragDropGroup('rules')
                ->columns(['2xl' => 3, 'md' => 2])
                ->gridSize(1)
                ->itemActions(fn() => [
                    $this->getRemoveAction(),
                    $this->getEventAddAction(),
                    $this->getTriggerAddAction(),
                ])
                ->schema([

                    ToggleButtons::make('is_or_mode')
                        ->inline()
                        ->label('')
                        ->required()
                        ->markAsRequired(false)
                        ->grouped()
                        ->boolean('Oder', 'Und')
                        ->colors(Color::Gray)
                        ->icons([]),

                    $this->getTriggerDropComponent()
                        ->columnStart(1),
                    $this->getEventDropComponent(),
                ])
        ]);

    }

    protected function getEventAddAction(): Action
    {
        return Action::make('addEvent')
            ->icon('heroicon-o-play-circle')
            ->label('Ausführung') //ToDo Translate
            ->action($this->addEvent(...))
            ->link();
    }


    //Events

    protected function getTriggerAddAction(): Action
    {
        return Action::make('addTrigger')
            ->icon('tabler-circuit-switch-open')
            ->label('Auslöser') //ToDo Translate
            ->action($this->addTrigger(...))
            ->link();
    }

    protected function getTriggerDropComponent(): DragDropComponent
    {
        return DragDropComponent::make('triggers')
            ->label('Auslöser')
            ->deepColor(1)
            ->orderAttribute('order')
            //->dragDropGroup('triggers')
            ->dragDropGroup(fn() => uniqid())
            ->itemActions(fn() => [
                $this->getRemoveAction(),
                $this->getTriggerInvertAction()
            ])
            ->itemLabel(fn($itemState
            ) => empty($itemState['type']) ? '' : $this->getTrigger($itemState['type'])->getDisplayName())
            ->schema([
                Select::make('type')
                    ->afterStateUpdated(fn($set) => $set('data', []))
                    ->options($this->getTriggerOptions(...))
                    ->selectablePlaceholder()
                    ->nullable(false)
                    ->label('')
                    ->required()
                    ->live(),

                Group::make()
                    ->statePath('data')
                    ->schema(function ($get) {
                        if (empty($get('type'))) {
                            return [];
                        }
                        $trigger = collect($this->getTriggers())
                            ->filter(fn(TriggerType $trigger) => $trigger::identifier() === $get('type'))
                            ->first();
                        if (is_null($trigger)) {
                            return [];
                        }
                        /**@var TriggerType $trigger */
                        return $trigger->getFormSchema();
                    })
            ]);
    }

    protected function getTriggerInvertAction(): Action
    {
        return Action::make('invert')
            ->action($this->invertTrigger(...))
            ->tooltip('Invertieren') //ToDo Translate
            ->label('')
            ->link()
            ->icon(function ($get, $arguments) {
                if ($get($arguments['item'] . '.is_inverted')) {
                    return 'carbon-warning-alt-inverted-filled';
                } else {
                    return 'tabler-triangle-inverted';
                }
            });
    }

    protected function getTriggerOptions(): array
    {
        return collect($this->getTriggers())
            ->map(fn(TriggerType $trigger) => [
                'identifier' => $trigger::identifier(),
                'label' => $trigger->getDisplayName(),
            ])
            ->pluck('label', 'identifier')
            ->toArray();
    }

    protected function addTrigger($set, $get, $arguments): void
    {
        $path = $arguments['item'] . '.triggers';
        $triggers = $get($path);
        if (is_null($triggers)) {
            $triggers = [];
        }
        $triggers[uniqid()] = ['order' => 1, 'is_inverted' => false];
        $set($path, $triggers);


        Notification::make()
            ->title('Auslöser hinzugefügt') //toDo Translate
            ->icon('tabler-circuit-switch-open')
            ->send();
    }

    protected function invertTrigger($set, $get, $arguments): void
    {
        $trigger = $get($arguments['item']);
        $trigger['is_inverted'] = !($trigger['is_inverted'] ?? false);
        $set($arguments['item'], $trigger);
    }

    protected function addEvent($set, $get, $arguments): void
    {
        $path = $arguments['item'] . '.events';
        $events = $get($path);
        if (is_null($events)) {
            $events = [];
        }
        $events[uniqid()] = ['order' => 1];
        $set($path, $events);

        Notification::make()
            ->title('Aktion hinzugefügt') //toDo Translate
            ->icon('heroicon-o-play-circle')
            ->send();
    }

    protected function getEventOptions(): array
    {
        return collect($this->getEvents())
            ->map(fn(EventType $events) => [
                'identifier' => $events::identifier(),
                'label' => $events->getDisplayName(),
            ])->pluck('label', 'identifier')
            ->toArray();
    }

    private function getRemoveAction(): Action
    {
        return Action::make('remove_')
            ->icon('heroicon-c-trash')
            ->iconButton()
            ->color(Color::Red)
            ->action(function ($arguments, $component, $get, $set) {
                $key = $arguments['item'];

                //Delete Structure
                $path = explode('.', $component->getStatePath());
                $path = '../' . $path[count($path) - 1];
                $state = $get($path);

                unset($state[$key]);

                $set($path, $state);
            });
    }


}
