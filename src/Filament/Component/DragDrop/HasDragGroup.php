<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;
use phpseclib3\File\ASN1\Maps\UniqueIdentifier;

trait HasDragGroup
{

    protected string|Closure|null $dragDropGroup = null;

    public function getDragDropGroup(): string
    {
        $dragDropGroup = $this->evaluate($this->dragDropGroup);
        if(is_null($this->dragDropGroup)) $dragDropGroup = uniqid();
        return $dragDropGroup;
    }

    public function dragDropGroup(Closure|string $dragDropGroup): static
    {
        $this->dragDropGroup = $dragDropGroup;
        return $this;
    }

}
