<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Component;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;

class DragDropActions extends Actions
{
    use HasDragGroup;
    use HasAlignment;
    use HasVerticalAlignment;


    public function actions(array $actions): static
    {
        $this->childComponents(function () use ($actions) {
            $group = $this->getDragDropGroup();
            return array_map(static function (DragDropAction $action) use ($group): Component {
                return $action->toFormComponent()->dragDropGroup($group);
            }, $actions);
        });

        return $this;
    }
}
