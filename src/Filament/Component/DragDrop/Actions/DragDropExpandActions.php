<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\OLDRepeaterFieldAction\Actions\NewEggActionComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\CanDisableOptions as HasDisableOptions;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Contracts\CanDisableOptions;
use Filament\Support\Colors\Color;

class DragDropExpandActions extends Component implements CanDisableOptions
{
    use HasOptions;
    use HasDisableOptions;
    use HasDragGroup;

    protected Closure $action;
    protected Closure|array $color;
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop.actions.expand_actions';
    protected string|null $name = null;

    public static function make(): static
    {
        $component = new static();
        $component->configure();
        return $component;
    }

    public function getActionsPath(): string
    {
        return $this->getChildComponentContainers()[0]->getComponents()[0]->getStatePath();
    }

    public function getRawActions(): Actions
    {
        $actions = [];

        foreach ($this->getOptions() as $option => $label) {
            $action = $this->evaluate($this->action, ['option' => $option])
                ->extraAttributes(['option' => $option]);
            /** @var Action $action */
            if (is_null($this->name)) {
                $this->name = $action->getName();
            }
            $actions[] = $action->name($this->name . '-' . $option);
        }

        return Actions::make($actions);
    }

    public function action(Closure $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function color(Closure|array $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getColor(): array
    {
        return $this->evaluate($this->color);
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->color(Color::Amber);
        $this->childComponents(fn() => [$this->getRawActions()]);
    }


    /* abstract function getDisabledColor(): string;

     abstract function getAdderId():string;

     abstract function getBorderColor(): string;
     abstract function getHoverColor(): string;*/


}
