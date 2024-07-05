<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\HasNestingInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

trait HasDragDropNestedFlattenList //ToDo if not flatten order and stuff
{

    protected string|Closure|null $nestedFlattenListType = null;
    protected bool|Closure $flatten = false;
    protected string|Closure $flattenView = "filament-package_ffhs_custom_forms::filament.components.drag-drop.default-container";

    protected int|Closure $gridSize = 1;
    protected bool|Closure $isFlattenViewHidden;


    protected null|string|Closure $orderAttribute = null;

    public function getNestedFlattenListType(): string
    {
        $type  = $this->evaluate($this->nestedFlattenListType);
        if(is_null($type)) return HasNestingInformation::class;
        return $type;
    }

    public function isFlatten(): bool
    {
        $flatten  = $this->evaluate($this->flatten);
        if(is_null($flatten)) return false;
        return $flatten;
    }

    public function flatten(bool|Closure $flatten = true): static
    {
        $this->flatten = $flatten;
        return $this;
    }


    public function getStructure(): array {

        if($this->isFlatten()) {
            $list = NestedFlattenList::make($this->getState(), CustomField::class);
            return $list->getStructure(true);
        }

        $orderBy = $this->getOrderAttribute();
        $state = $this->getState();

        if(is_null($orderBy)) return $state;

        return collect($state)->sortBy($orderBy)->toArray();
    }

    public function flattenView(bool|Closure $flattenView): static
    {
        $this->flattenView = $flattenView;
        return $this;
    }

    public function getFlattenView($key): string
    {
        return $this->evaluate($this->flattenView, $this->getItemInjection($key));
    }

    public function flattenViewHidden(bool|Closure $isFlattenViewHidden): static
    {
        $this->isFlattenViewHidden = $isFlattenViewHidden;
        return $this;
    }

    public function isFlattenViewHidden($key): string
    {
        return $this->evaluate($this->isFlattenViewHidden, $this->getItemInjection($key));
    }




    public function orderAttribute(string|Closure $orderAttribute): static
    {
        $this->orderAttribute = $orderAttribute;
        return $this;
    }

    public function getOrderAttribute(): ?string
    {
        return $this->evaluate($this->orderAttribute);
    }



}
