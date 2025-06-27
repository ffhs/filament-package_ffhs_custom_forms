<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasDragDropItemActions
{
    protected array|Closure $itemActions = [];

    public function getItemActions($itemKey): array|Closure|null
    {
        return $this->evaluate($this->itemActions, namedInjections: $this->getItemInjection($itemKey));
    }

    public function itemActions(array|Closure $itemActions): static
    {
        $this->itemActions = $itemActions;
        return $this;
    }


    public function getItemActionContainer($key): ComponentContainer
    {
        return $this->getChildComponentContainers()[$key.'-actions'];
    }


    protected function generateItemActions(string $key): ComponentContainer {
        $actions = $this->getItemActions($key);

        $components = array_map(fn(Action $action) => $action->mergeArguments(['item' => $key]), $actions);

        return ComponentContainer::make($this->getLivewire())
            ->getClone()
            ->parentComponent($this)
            ->components([Actions::make($components)->columnSpanFull()->alignment(Alignment::Right)]);

    }

}
