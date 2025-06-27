<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasItemLabel
{
    protected string|Closure|null $itemLabel = '';

    public function getItemLabel($itemKey): string|Closure|null
    {
        return $this->evaluate($this->itemLabel, $this->getItemInjection($itemKey));
    }

    public function itemLabel(null|string|Closure $itemLabel): static
    {
        $this->itemLabel = $itemLabel;
        return $this;
    }

}
