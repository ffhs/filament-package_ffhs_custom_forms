<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;

trait HasDragGroup
{
    protected string|Closure|null $dragDropGroup = null;

    public function getDragDropGroup(): string
    {
        $dragDropGroup = $this->evaluate($this->dragDropGroup);
        if (is_null($this->dragDropGroup)) {
            $dragDropGroup = uniqid();
        }
        return $dragDropGroup;
    }

    public function dragDropGroup(Closure|string $dragDropGroup): static
    {
        $this->dragDropGroup = $dragDropGroup;
        return $this;
    }
}
