<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;


use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\HasStateBindingModifiers;

class DragDropComponent extends Field {
    use HasStateBindingModifiers;
    use HasDragDropItemActions;
    use HasDragDropItemContainers;
    use HasDragDropItemIcon;
    use HasDragDropItemLabel;
    use HasDragDropItemGrid;
    use HasDragDropNestedFlattenList;


    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop.index';

    protected string|Closure $dragDropGroup;


    protected function setUp(): void
    {
        $this->itemLabel(fn($item)=> $item);
        $this->dragDropGroup(uniqid());

        $this->flattenViewHidden(fn($item, $state) =>
            empty($state[$item][$this->getNestedFlattenListType()::getEndContainerPositionAttribute()])
        );

    }

    public function getDragDropGroup(): string
    {
        return $this->evaluate($this->dragDropGroup);
    }

    public function dragDropGroup(Closure|string $dragDropGroup): static
    {
        $this->dragDropGroup = $dragDropGroup;
        return $this;
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'dragDropGroup' => [$this->getDragDropGroup()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };

    }

    protected function getItemInjection($key): array
    {
        return ['item' => $key, 'itemState' => $this->getState()[$key] ?? []];
    }


    public function getChildComponentContainers(bool $withHidden = false): array
    {
       // dd($this->getLivewire()->getForm('form')->getFlatComponents());

        $containers = [];

        foreach ($this->getState() ?? [] as $key => $element) {
            $containers[$key] = $this->generateItemContainer($key);
            $containers[$key. "-actions"] = $this->generateItemActions($key);
        }

        return $containers;
    }




    public function nestedFlattenListType(null|Closure|string $nestedFlattenList):static {
        $this->nestedFlattenListType = $nestedFlattenList;
        return $this;
    }



    public function getState(): mixed
    {
        $state = parent::getState();
        if(is_null($state)) return [];
        return $state;
    }


}
