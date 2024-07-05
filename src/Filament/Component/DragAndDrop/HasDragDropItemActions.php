<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasDragDropItemActions
{
    protected array|Closure $itemActions = [];
    protected array $actionContainers = [];


    public function getItemActions($itemKey): array|Closure|null
    {
        return $this->evaluate($this->itemActions, namedInjections: $this->getItemInjection($itemKey));
    }

    public function itemActions(array|Closure $itemActions): static
    {
        $this->itemActions = $itemActions;
        return $this;
    }

    public function getItemActionContainers():array
    {
        return $this->actionContainers;
    }


    public function getItemActionContainer($key): ComponentContainer
    {
        $actions = $this->getActionContainers();

        if(array_key_exists($key, $actions)) return $actions[$key];
        $this->generateItemActions($key);
        return $this->getActionContainers()[$key];
    }


    protected function generateItemActions(string $key): void {
        $actions = $this->getItemActions($key);

        $components = array_map(fn(Action $action) => $action->mergeArguments(["item" => $key]), $actions);

        $container = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->statePath($key)
            ->components([Actions::make($components)->columnSpanFull()->alignment(Alignment::Right)]);

        $this->actionContainers[$key] = $container;
    }

}
