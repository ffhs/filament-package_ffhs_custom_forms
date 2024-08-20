<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Colors\Color;
use Ramsey\Collection\Collection;

class RuleEditor extends Group
{

    protected array|Closure|null $triggers;
    protected array|Closure|Collection $targets;
    protected Closure|array|null $events;


    protected function setUp(): void
    {
        parent::setUp();

        $this->triggers(config("ffhs_custom_forms.rule.trigger"));
        $this->events(config("ffhs_custom_forms.rule.event"));

        $this->schema([
            Action::make('AddRule')
                ->action(function ($set,$get) {
                    $rules = $get('rules');
                    $rules[uniqid()] = [
                        'order' => 1,
                        'is_oder_mode' => false,
                    ];
                    $set('rules', $rules);
                })->toFormComponent(),
           DragDropComponent::make('rules')
               ->itemLabel("Regel")
               ->orderAttribute('order')
               ->dragDropGroup('rules')
               ->columns(1)
               ->gridSize(3)
               ->itemActions(fn()=> [
                   $this->getTriggerAddAction(),
                   $this->getEventAddAction(),
               ])
               ->schema([

                   ToggleButtons::make('is_oder_mode')
                       ->inline()
                       ->label("")
                       ->grouped()
                       ->boolean("Oder", "Und")
                       ->default(false)
                       ->colors(Color::Gray)
                       ->icons([]),

                   $this->getTriggerDropComponent(),
                   $this->getEventDropComponent(),

                   Select::make("targets")
                        ->multiple()
                        ->live()
                        ->options($this->getTargets(...))
                ])
        ]);

    }


    public function getTargets(){
        return $this->evaluate($this->targets);
    }
    public function targets(array|Closure|Collection $targets): RuleEditor
    {
         $this->targets = $targets;
        return $this;
    }


    protected function getTriggerDropComponent(): DragDropComponent
    {
        return DragDropComponent::make("triggers")
            ->label("Auslösser")
            ->deepColor(1)
            ->orderAttribute('order')
            ->dragDropGroup('triggers')
            ->itemActions([
                $this->getTriggerInvertAction()
            ])
            ->itemLabel(fn($itemState) => empty($itemState["type"])? "": $this->getTrigger($itemState['type'])->getDisplayName())
            ->schema([
                Select::make('type')
                    ->selectablePlaceholder(true)
                    ->nullable(false)
                    ->options($this->getTriggerOptions(...))
                    ->live(),
                Group::make()
                    ->statePath('data')
                    ->schema(function($get) {
                        if(empty($get('type'))) return [];
                        $trigger =collect($this->getTriggers())
                            ->filter(fn(TriggerType $trigger) => $trigger::identifier() === $get('type'))
                            ->first();
                        if(is_null($trigger)) return [];
                        /**@var TriggerType $trigger*/
                        return $trigger->getFormSchema();
                    })
                ->live()
            ]);
    }


    protected function getTriggerOptions(): array
    {
        return collect($this->getTriggers())
            ->map(fn(TriggerType $trigger) => [
                    'identifier' => $trigger::identifier(),
                    'label' => $trigger->getDisplayName(),
                ])->pluck('label', 'identifier')
            ->toArray();
    }

    protected function getTriggerAddAction(): Action
    {
        return Action::make('addTrigger')
            ->action($this->addTrigger(...));
    }

    protected function addTrigger ($set, $get, $arguments): void {
            $path = $arguments['item'] . '.triggers';
            $triggers = $get($path);
            if(is_null($triggers)) $triggers = [];
            $triggers[uniqid()] = ['order' => 1, 'is_inverted' => false];
            $set($path, $triggers);
        }


    /**
     * @return TriggerType[]
     */
    public function getTriggers(): array
    {
        $types = $this->evaluate($this->triggers)??[];

        if(empty($types)) return [];

        if($types[0] instanceof TriggerType) return $types;

        $finalTypes = [];
        foreach ($types as $typeClass){
            $finalTypes[] = $typeClass::make();
        }

        return  $finalTypes;
    }

    public function triggers(array|Closure|null $triggers): static
    {
        $this->triggers = $triggers;
        return $this;
    }


    protected function getTriggerInvertAction(): Action
    {
        return Action::make("invert")
            ->action($this->invertTrigger(...))
            ->icon(
                fn($get, $arguments)=> $get($arguments["item"].".is_inverted")?"carbon-warning-alt-inverted-filled":"tabler-triangle-inverted")
            ->label("")
            ->tooltip("Invertieren") //ToDo Translate
            ->link();
    }

    protected function invertTrigger ($set, $get, $arguments): void {
        $trigger = $get($arguments['item']);
        $trigger["is_inverted"] = !$trigger["is_inverted"];
        $set($arguments['item'], $trigger);
    }


    //Events
    protected function getEventAddAction(): Action
    {
        return Action::make('addEvent')
            ->action($this->addEvent(...));
    }

    protected function addEvent ($set, $get, $arguments): void {
        $path = $arguments['item'] . '.events';
        $events = $get($path);
        if(is_null($events)) $events = [];
        $events[uniqid()] = ['order' => 1];
        $set($path, $events);
    }


    public function getEventDropComponent(): DragDropComponent
    {
        return DragDropComponent::make("events")
            ->label("Ausführung")
            ->dragDropGroup('events')
            ->deepColor(1)
            ->orderAttribute('order')
            ->itemLabel(fn($itemState) => empty($itemState["type"])? "": $this->getEvent($itemState['type'])->getDisplayName())
            ->schema([
                Select::make('type')
                    ->selectablePlaceholder(true)
                    ->nullable(false)
                    ->options($this->getEventOptions(...))
                    ->live(),
                Group::make()
                    ->statePath('data')
                    ->schema(function($get) {
                        if(empty($get('type'))) return [];
                        $trigger =collect($this->getEvents())
                            ->filter(fn(EventType $event) => $event::identifier() === $get('type'))
                            ->first();
                        if(is_null($trigger)) return [];
                        /**@var EventType $trigger*/
                        return $trigger->getFormSchema();
                    })
                    ->live()
            ]);
    }


    public function getEvents(): array
    {
        $types = $this->evaluate($this->events)??[];

        if(empty($types)) return [];

        if($types[0] instanceof EventType) return $types;

        $finalTypes = [];
        foreach ($types as $typeClass){
            $finalTypes[] = $typeClass::make();
        }

        return  $finalTypes;
    }

    public function events(array|Closure|null $events): static
    {
        $this->events = $events;
        return $this;
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

    public function getTrigger($type): TriggerType
    {
        return collect($this->getTriggers())->filter(fn(TriggerType $event) => $event::identifier() === $type)->first();
    }

    public function getEvent($type): EventType
    {
        return collect($this->getEvents())->filter(fn(EventType $event) => $event::identifier() === $type)->first();
    }


}
