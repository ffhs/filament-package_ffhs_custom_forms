<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasDragDropItemGrid
{

    protected int|Closure $gridSize = 1;

    public function getGridSize(): int
    {
        return $this->evaluate($this->gridSize);
    }


    public function gridSize(Closure|int $gridSize):static
    {
        $this->gridSize = $gridSize;
        return $this;
    }


    //ToDo size for single elements

}
