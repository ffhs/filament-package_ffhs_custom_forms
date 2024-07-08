<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions;
use Filament\Support\Facades\FilamentAsset;

class DragDropActionContainer extends Actions\ActionContainer
{
    use HasDragGroup;

    public function toHtml(): string
    {
        $html =  parent::toHtml();

        $toReplace = "
        <script src=\"".FilamentAsset::getScriptSrc('drag_drop_script', 'ffhs/filament-package_ffhs_custom_forms')."\"></script>

        <div
         ffhs_drag:drag
        ffhs_drag:group=\"". $this->getDragDropGroup()."\"
         x-init=\"setupDraggable(\$el)\"
        ";

        //Replace Button
        $html = str_replace('<button', $toReplace, $html);

        $html = str_replace('</button', '</div', $html);
        $html = str_replace('wire:target', 'ffhs_drag:action_target', $html);
        $html = str_replace('wire:click', 'ffhs_drag:action_click', $html);
        $html = str_replace('wire:click', 'cursor-grab', $html);
        $html = str_replace('style="', 'x-init="" draggable="true" style="cursor: grab !important; ', $html);


        // $html = str_replace('wire:loading.delay.default="" ', '', $html);
        //fi-btn
        return $html;
    }


}
