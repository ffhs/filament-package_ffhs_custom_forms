<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasDragDropItemLabel
{
    protected string|Closure $itemLabel;

    public function getItemLabel($itemKey): string|Closure
    {
        return $this->evaluate($this->itemLabel, $this->getItemInjection($itemKey));
    }

    public function itemLabel(array|Closure $itemLabel): static
    {
        $this->itemLabel = $itemLabel;
        return $this;
    }

}
