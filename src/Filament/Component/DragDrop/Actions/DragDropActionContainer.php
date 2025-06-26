<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Facades\FilamentAsset;
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


    public function toHtml(): string
    {
        $html = parent::toHtml();
        return $html;

        //Remove Action
        $action = explode('wire:click="', $html)[1];
        $action = explode('"', $action)[0];
        $action = html_entity_decode($action);
        $action = str_replace("'", "\'", $action);

        //ToDo make blade
        $toReplace = "

        <div
        ax-load
        ax-load-src=\"" . FilamentAsset::getAlpineComponentSrc("action", "ffhs/filament-package_ffhs_drag-drop") . "\"
        x-ignore
        x-data=\"dragDropAction('" . $this->getDragDropGroup() . "', '$action')\"
        ffhs_drag:component
        ";

        //Replace Button
        //ToDo make Blade
        $html = str_replace('<button', $toReplace, $html);

        $html = str_replace('</button', '</div', $html);

        $html = str_replace('wire:click', 'ffhs_drag:action', $html);

        $html = str_replace('wire:click', 'cursor-grab', $html);
        $html = str_replace('style="', 'x-init="" style="cursor: grab !important; ', $html);
        $html = str_replace('type="button" ', '', $html);
        $html = str_replace('wire:loading.attr="disabled" ', '', $html);

        $html = str_replace('<span x-init=""', '<span class="hidden xl:block"', $html);

        return $html;
    }

}
