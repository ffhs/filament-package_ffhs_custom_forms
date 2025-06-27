<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use PHPUnit\TextUI\RuntimeException;

class DragDropActionContainer extends Actions\ActionContainer
{
    use HasDragGroup;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop.actions.action-container';

    public static function make(DragDropAction|Action $action): static
    {
        if ($action instanceof DragDropAction) {
            return parent::make($action);
        }

        throw new RuntimeException('Action is not a DragDropAction');
    }
}
