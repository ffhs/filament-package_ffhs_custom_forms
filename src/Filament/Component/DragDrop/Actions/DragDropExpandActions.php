<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\OLDRepeaterFieldAction\Actions\NewEggActionComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanDisableOptions;
use Filament\Forms\Components\Concerns\CanDisableOptions as HasDisableOptions;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Support\Colors\Color;

class DragDropExpandActions extends Component implements CanDisableOptions
{
    use HasOptions;
    use HasDisableOptions;
    use HasDragGroup;

    protected Closure $action;
    protected Closure|array $color;
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop.actions.expand_actions';

    public static function make():static
    {
        $component = new static();
        $component->configure();
        return $component;
    }

    protected function setUp(): void {
        parent::setUp();
        $this->color(Color::Amber);
        $this->childComponents(fn()=> [$this->getRawActions()]);
    }


    public function getActionsPath(): string
    {
        return $this->getChildComponentContainers()[0]->getComponents()[0]->getStatePath();
    }



    public function getRawActions(): Actions
    {
        $actions  = [];
        foreach ($this->getOptions() as $option => $label) {
            $actions[] = $this->evaluate($this->action, ['option' => $option])
                ->name($option)
                ->extraAttributes(['option' => $option]);
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

    public function getColor():array
    {
        return $this->evaluate($this->color);
    }






   /* abstract function getDisabledColor(): string;

    abstract function getAdderId():string;

    abstract function getBorderColor(): string;
    abstract function getHoverColor(): string;*/



}
