<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;


use ArrayObject;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\HasNestingInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\HasStateBindingModifiers;
use Illuminate\Support\Collection;

class DragDropComponent extends Field {
    use HasStateBindingModifiers;
    use HasDragDropItemActions;
    use HasDragDropItemContainers;


    protected null|string|Closure $itemIcons = null;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop';

    protected string|Closure $dragDropGroup;

    protected string|Closure $itemLabel;

    protected bool|Closure $useFlattenList = false;

    protected int|Closure $gridSize = 1;
    private string|Closure|null $nestedFlattenListType = null;

    protected bool $childrenGenerated = false;


    protected function setUp(): void
    {
        $this->itemLabel(fn($item)=> $item);
        $this->dragDropGroup(uniqid());

        $this->childComponents(function ($get): array {
            $this->generateChildContainers();
            $containers = $this->getCombinedContainers();

            return $containers
                ->map(fn(ComponentContainer $container) => $container->getComponents())
                ->flatten(1)
                ->toArray();
        });

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


    public function getItemLabel($itemKey): string|Closure
    {
        return $this->evaluate($this->itemLabel, $this->getItemInjection($itemKey));
    }

    public function itemLabel(array|Closure $itemLabel): static
    {
        $this->itemLabel = $itemLabel;
        return $this;
    }



    public function getUseFlattenList(): bool|Closure
    {
        return $this->evaluate($this->useFlattenList);
    }

    public function flattened(bool|Closure $flattened): void
    {
        $this->useFlattenList = $flattened;
    }

  /*  public function getChildComponents(): array
    {
        $this->generateChildContainers();
        $containers = $this->getCombinedContainers();


        $return = $containers
            ->map(fn(ComponentContainer $container) => $container->getComponents())
            ->flatten(1)
            ->toArray();

        Debugbar::info($containers);

        return $return;
    }*/


    protected function getCombinedContainers(): Collection {
        return collect([
            ...array_values($this->getActionContainers()),
            ...array_values($this->getItemContainers()),
        ]);
    }






    public function getGridSize(): int
    {
        return $this->evaluate($this->gridSize);
    }


    public function gridSize(Closure|int $gridSize):static
    {
        $this->gridSize = $gridSize;
        return $this;
    }

    public function getItemIcon($itemKey): Closure|string|null
    {
        return $this->evaluate($this->itemIcons, $this->getItemInjection($itemKey));
    }

    public function itemIcons(Closure|string|null $itemIcons): static
    {
        $this->itemIcons = $itemIcons;
        return $this;
    }


    public function getNestedFlattenListType(): string
    {
        $type  = $this->evaluate($this->nestedFlattenListType);
        if(is_null($type)) return HasNestingInformation::class;
        return $type;
    }

    public function nestedFlattenListType(null|Closure|string $nestedFlattenList):static {
        $this->nestedFlattenListType = $nestedFlattenList;
        return $this;
    }


    public function getStructure(): array {
        $list = NestedFlattenList::make($this->getState(), $this->getNestedFlattenListType());
        return $list->getStructure(true);
    }

    protected function generateChildContainers(): void {
        if($this->isChildrenGenerated()) return;

        $this->itemContainers = [];
        $this->actionContainers = [];

        foreach ($this->getState() as $key => $field) {
            $this->generateItemActions($key);
            $this->generateItemContainer($key);
        }

        $this->childrenGenerated = true;
    }

    protected function isChildrenGenerated(): bool
    {
        return $this->childrenGenerated;
    }

    public function getState(): mixed
    {
        $state = parent::getState();
        if(is_null($state)) return [];
        return $state;
    }


}
