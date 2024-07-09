<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Colors\Color;

class RuleEditor extends Group
{

    protected array|Closure|null $triggers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->triggers(config("ffhs_custom_forms.rule.trigger"));

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
                   $this->getTriggerAddAction()
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

                    DragDropComponent::make("events")->label("Ausführung")
                        ->dragDropGroup('events')
                        ->deepColor(1),
                ])
        ]);

    }


    protected function getTriggerDropComponent(): DragDropComponent
    {
        return DragDropComponent::make("triggers")
            ->label("Auslösser")
            ->deepColor(1)
            ->orderAttribute('order')
            ->dragDropGroup('triggers')
            ->schema([
                Select::make('type')
                    ->selectablePlaceholder(false)
                    ->nullable(false)
                    ->options($this->getTriggerOptions(...))
                    ->live(),
                Group::make()
                    ->statePath('data')
                    ->schema(fn($get) => $this->getTriggers()[$get['type']]->getFormSchema())
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




}
