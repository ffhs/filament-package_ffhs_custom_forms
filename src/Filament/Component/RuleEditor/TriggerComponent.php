<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Ffhs\FfhsUtils\Filament\DragDrop\DragDropGroup;
use Ffhs\FfhsUtils\Traits\CanLoadDefaultFromComponents;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field\ElevatedActions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;

class TriggerComponent extends DragDropGroup
{
    use HasRuleTriggers;
    use CanLoadDefaultFromComponents;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Auslöser'); //ToDo Translate
        $this->itemLabel(fn($itemState) => empty($itemState['type']) ? '' : $this
            ->getTrigger($itemState['type'])
            ?->getDisplayName() ?? ''
        );

        $this->schema([
            ElevatedActions::make([
                RuleRemoveAction::make(),
                $this->getTriggerInvertAction(),
            ]),
            Select::make('type')
                ->options($this->getTriggerOptions(...))
                ->selectablePlaceholder()
                ->nullable(false)
                ->required()
                ->label('')
                ->live()
                ->afterStateUpdated(function ($set, $state) {
                    $components = $this->getTrigger($state)?->getFormSchema() ?? [];
                    $default = $this->loadDefaultFromComponents($components, null);
                    $set('data', $default);
                }),
            Group::make()
                ->statePath('data')
                ->schema(fn($get) => $this->getTrigger($get('type'))?->getFormSchema() ?? [])
        ]);
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

    protected function getTriggerInvertAction(): Action
    {
        return Action::make('invert')
            ->icon(fn($get) => $get('is_inverted') ? 'carbon-warning-alt-inverted-filled' : 'tabler-triangle-inverted')
            ->action(fn($set, $get) => $set('is_inverted', !$get('is_inverted')))
            ->tooltip('Invertieren') //ToDo Translate
            ->iconButton();
    }

}
