<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasItemGrid
{

    protected int|Closure $gridSize = 1;
    protected int|Closure $itemGridSize = 1;
    protected int|Closure $flattenGrid = 1;
    protected int|Closure|null $itemGridStart = null;


    public function getGridSize(): int
    {
        return $this->evaluate($this->gridSize);
    }


    public function gridSize(Closure|int $gridSize):static
    {
        $this->gridSize = $gridSize;
        return $this;
    }

    public function itemGridSize(Closure|int $gridSize):static
    {
        $this->itemGridSize = $gridSize;
        return $this;
    }
    public function flattenGrid(Closure|int $flattenGrid):static
    {
        $this->flattenGrid = $flattenGrid;
        return $this;
    }

    public function itemGridStart(Closure|int $gridSize):static
    {
        $this->itemGridStart = $gridSize;
        return $this;
    }

    public function getItemGridSize($key): int
    {
        return $this->evaluate($this->itemGridSize, $this->getItemInjection($key)) ?? 1;
    }


    public function getItemGridStart($key): int|null
    {
        return $this->evaluate($this->itemGridStart, $this->getItemInjection($key));
    }

    public function getFlattenGrid($key): int|null
    {
        return $this->evaluate($this->flattenGrid, $this->getItemInjection($key));
    }







}
