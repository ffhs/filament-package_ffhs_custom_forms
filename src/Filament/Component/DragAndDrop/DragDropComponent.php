<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;


use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\HasNestingInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestingObject;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Collection;

class DragDropComponent extends Field {

    protected null|string|Closure $itemIcons = null;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop';

    protected string|Closure $dragDropGroup;

    protected array|Closure $itemActions = [];

    protected string|Closure $itemLabel;

    protected bool|Closure $useFlattenList = false;

    protected int|Closure $gridSize = 1;

    protected array $itemContainers = [];
    protected array $actionContainers = [];

    protected array | Closure $itemSchema = [];
    private string|Closure|null $nestedFlattenListType = null;


    protected function setUp(): void
    {
        $this->itemLabel(fn($item)=> $item);
        $this->dragDropGroup(uniqid());
    }

    public function getItemContainer($itemKey): ComponentContainer
    {
        $components = $this->getChildComponents();

        if(array_key_exists($itemKey, $components)) return $components[$itemKey];
        $this->generateItemContainer($itemKey);
        return $this->getItemContainers()[$itemKey];
    }

    public function getActionContainer($itemKey): ComponentContainer
    {
        $actions = $this->getActionContainers();

        if(array_key_exists($itemKey, $actions)) return $actions[$itemKey];
        $this->generateItemActions($itemKey);
        return $this->getActionContainers()[$itemKey];
    }

    protected function generateItemActions(string $key): void {
        $actions = $this->getItemActions($key);

        $components = array_map(fn(Action $action) => $action->mergeArguments(["item" => $key]), $actions);

        $container = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->components([Actions::make($components)->columnSpanFull()->alignment(Alignment::Right)]);

        $this->actionContainers[$key] = $container;
    }


    protected function generateItemContainer(string $key): void {
        $components = $this->getItemSchema($key);

        $container = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->statePath($key)
            ->components($components);

        $this->itemContainers[$key] = $container;
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

    public function getItemActions($itemKey): array|Closure|null
    {
        return $this->evaluate($this->itemActions, namedInjections: $this->getItemInjection($itemKey));
    }

    public function itemActions(array|Closure $itemActions): static
    {
        $this->itemActions = $itemActions;
        return $this;
    }

    protected function getItemInjection($itemKey): array
    {
        return ['item' => $itemKey, 'itemState' => $this->getState()[$itemKey] ?? []];
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

    public function getChildComponents(): array
    {
        $containers = $this->getCombinedContainers();

        return $containers
            ->map(fn(ComponentContainer $container) => $container->getComponents())
            ->flatten(1)
            ->toArray();
    }


    public function getChildComponentContainers(bool $withHidden = false): array
    {
        return $this->getCombinedContainers()->toArray();
    }


    protected function getCombinedContainers(): Collection {
        return collect([
            ...array_values($this->getActionContainers()),
            ...array_values($this->getItemContainers()),
        ]);
    }

    public function getActionContainers():array
    {
        return $this->actionContainers;
    }

    public function getItemContainers():array
    {
       return  $this->itemContainers;
    }


    public function schema(array | Closure $schema): static
    {
        $this->itemSchema = $schema;
        return $this;
    }

    public function getItemSchema(string $key): array
    {
        return $this->evaluate($this->itemSchema, $this->getItemInjection($key));
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

    public function setItemIcons(Closure|string|null $itemIcons): static
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


}
