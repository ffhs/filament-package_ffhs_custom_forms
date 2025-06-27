<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\HasStateBindingModifiers;

class DragDropComponent extends Field
{
    use HasStateBindingModifiers;
    use HasDragDropItemActions;
    use HasDragDropItemContainers;
    use HasItemIcon;
    use HasItemLabel;
    use HasItemGrid;
    use HasNestedFlattenList;
    use HasDragGroup;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop.index';
    protected int $deepColor = 0;

    public function deepColor(int $deepColor): static
    {
        $this->deepColor = $deepColor;

        return $this;
    }

    public function getDeepColor(): int
    {
        return $this->evaluate($this->deepColor) ?? 0;
    }

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        $containers = [];

        foreach ($this->getState() ?? [] as $key => $element) {
            $containers[$key] = $this->generateItemContainer($key);
            $containers[$key . '-actions'] = $this->generateItemActions($key);
        }

        return $containers;
    }

    public function nestedFlattenListType(null|Closure|string $nestedFlattenList): static
    {
        $this->nestedFlattenListType = $nestedFlattenList;

        return $this;
    }

    public function getState(): mixed
    {
        $state = parent::getState();

        if (is_null($state)) {
            return [];
        }

        return $state;
    }

    protected function setUp(): void
    {
        $this
            ->itemLabel(fn($item) => $item)
            ->dragDropGroup(uniqid())
            ->flattenViewHidden(fn($item, $state) => empty(
            $state[$item][$this->getNestedFlattenListType()::getEndContainerPositionAttribute()]
            ));
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
}
