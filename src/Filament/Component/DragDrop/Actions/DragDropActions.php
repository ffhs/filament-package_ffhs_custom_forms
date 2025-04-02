<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
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
        $this->childComponents(fn()=> array_map(
            fn (Action $action): Component => DragDropActionContainer::make($action)->dragDropGroup($this->getDragDropGroup()),
            $actions,
        ));

        return $this;
    }

}
