<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasItemIcon
{
    protected null|string|Closure $itemIcons = null;

    public function getItemIcon($itemKey): Closure|string|null
    {
        return $this->evaluate($this->itemIcons, $this->getItemInjection($itemKey));
    }
    public function itemIcons(Closure|string|null $itemIcons): static
    {
        $this->itemIcons = $itemIcons;
        return $this;
    }


}
