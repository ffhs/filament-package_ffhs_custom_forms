<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\FlattedNestedList\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\FlattedNestedList\NestedListElement;

trait HasNestedFlattenList //ToDo if not flatten order and stuff
{

    protected string|Closure|null $nestedFlattenListType = null;
    protected bool|Closure $flatten = false;
    protected string|Closure $flattenView = 'filament-package_ffhs_custom_forms::filament.components.drag-drop.container';

    protected int|Closure $gridSize = 1;
    protected bool|Closure $isFlattenViewHidden = false;
    protected null|string|Closure $orderAttribute = null;
    protected null|string|Closure $flattenViewLabel = null;

    public function flatten(bool|Closure $flatten = true): static
    {
        $this->flatten = $flatten;
        return $this;
    }

    public function getStructure(): array
    {

        if ($this->isFlatten()) {
            $list = NestedFlattenList::make($this->getState(), $this->getNestedFlattenListType());
            return $list->getStructure(true);
        }

        $orderBy = $this->getOrderAttribute();
        $state = $this->getState();

        if (is_null($orderBy)) {
            return $state;
        }

        return collect($state)->sortBy($orderBy)->toArray();
    }

    public function isFlatten(): bool
    {
        $flatten = $this->evaluate($this->flatten);
        if (is_null($flatten)) {
            return false;
        }
        return $flatten;
    }

    public function getNestedFlattenListType(): string
    {
        $type = $this->evaluate($this->nestedFlattenListType);
        if (is_null($type)) {
            return NestedListElement::class;
        }
        return $type;
    }

    public function getOrderAttribute(): ?string
    {
        return $this->evaluate($this->orderAttribute);
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

    public function flattenViewLabel(string|Closure $flattenViewLabel): static
    {
        $this->flattenViewLabel = $flattenViewLabel;
        return $this;
    }

    public function orderAttribute(string|Closure $orderAttribute): static
    {
        $this->orderAttribute = $orderAttribute;
        return $this;
    }

    public function isFlattenViewHidden($key): string
    {
        return $this->evaluate($this->isFlattenViewHidden, $this->getItemInjection($key));
    }

    public function getFlattenViewLabel($key): ?string
    {
        return $this->evaluate($this->flattenViewLabel, $this->getItemInjection($key));
    }


}
