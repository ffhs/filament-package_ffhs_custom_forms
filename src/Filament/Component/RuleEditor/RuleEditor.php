<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Ffhs\FfhsUtils\Filament\DragDrop\DragDropGroup;
use Ffhs\FfhsUtils\Traits\HasGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field\ElevatedActions;
use Filament\Actions\Action;
use Filament\Forms\Components\Concerns\CanGenerateUuids;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Support\Colors\Color;


class RuleEditor extends Component
{
    use HasRuleTriggers;
    use HasRuleEvents;
    use HasGroup;
    use CanGenerateUuids;

    protected string $view = 'filament-schemas::components.grid';
    protected string $childKey;

    public static function make($key)
    {
        $static = app(static::class);

        $static->childKey = $key;
        $static->configure();

        return $static;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRuleEditorSchema(): array
    {
        return [
            $this->getAddRuleAction(),

            DragDropGroup::make($this->childKey)
                ->hiddenLabel()
                ->itemIcons('carbon-rule')
                ->itemLabel(function ($itemState) {
                    $triggers = count($itemState['triggers'] ?? []);
                    $event = count($itemState['events'] ?? []);

                    return 'Regel (' . $triggers . 'T : ' . $event . 'E)'; //ToDo Translate
                })
                ->group($this->getGroup())
                ->columns(3)
                ->itemSize(2)
                ->schema([
                    Group::make([
                        ToggleButtons::make('is_or_mode')
                            ->inline()
                            ->label('')
                            ->required()
                            ->markAsRequired(false)
                            ->grouped()
                            ->boolean('Oder', 'Und')
                            ->colors(Color::Gray)
                            ->icons([]),
                        ElevatedActions::make([
                            RuleRemoveAction::make(),
                            $this->getEventAddAction(),
                            $this->getTriggerAddAction(),
                        ]),
                        TriggerComponent::make('triggers')
                            ->group(fn() => $this->getGroup() . '-triggers')
                            ->triggers(fn() => $this->getTriggers()),
                        EventComponent::make('events')
                            ->group(fn() => $this->getGroup() . '-events')
                            ->events(fn() => $this->getEvents()),
                    ])
                        ->columnSpanFull()
                        ->columns(),

                ])
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->schema($this->getRuleEditorSchema());
    }

    protected function getEventAddAction(): Action
    {
        return Action::make('addEvent')
            ->icon('heroicon-o-play-circle')
            ->label('Ausführung') //ToDo Translate
            ->link()
            ->action(function ($set): void {
                $set('events.' . $this->generateUuid(), ['order' => 1]);

                Notification::make()
                    ->title('Aktion hinzugefügt')
                    ->icon('heroicon-o-play-circle')
                    ->send();
            });
    }

    //Events
    protected function getTriggerAddAction(): Action
    {
        return Action::make('addTrigger')
            ->icon('tabler-circuit-switch-open')
            ->label('Auslöser') //ToDo Translate

            ->link()
            ->action(function ($set): void {
                $set('triggers.' . $this->generateUuid(), ['order' => 1, 'is_inverted' => false]);

                Notification::make()
                    ->title('Auslöser hinzugefügt') //toDo Translate
                    ->icon('tabler-circuit-switch-open')
                    ->send();
            });
    }

    protected function getAddRuleAction(): Action
    {
        return Action::make('add_rule')
            ->icon('carbon-rule')
            ->label('Regel Hinzufügen')  //ToDo translate
            ->action(function ($get, $set) {
                $rule = ['is_oder_mode' => false, 'triggers' => [], 'events' => []];
                $set($this->childKey . '.' . $this->generateUuid(), $rule);
            });
    }
}
