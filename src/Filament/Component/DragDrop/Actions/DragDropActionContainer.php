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

        //ToDo  Fixing with alpine components
        $toReplace = "

        <div
         ffhs_drag:drag
        ffhs_drag:group=\"". $this->getDragDropGroup()."\"

        ax-load
        ax-load-src=\"".FilamentAsset::getAlpineComponentSrc("drag_drop_element", "ffhs/filament-package_ffhs_custom_forms")."\"
        x-ignore
        x-data=\"dragDropElement(true)\"
        ";




        //Replace Button
        $html = str_replace('<button', $toReplace, $html);

        $html = str_replace('</button', '</div', $html);
        $html = str_replace('wire:click', 'ffhs_drag:action', $html);
        $html = str_replace('wire:click', 'cursor-grab', $html);
        $html = str_replace('style="', 'x-init="" draggable="true" style="cursor: grab !important; ', $html);
        $html = str_replace('type="button" ', '', $html);
        $html = str_replace('wire:loading.attr="disabled" ', '', $html);

        $html = str_replace('x-data="{}"', '', $html);
//
//


        /*
         *
         */


        // $html = str_replace('wire:loading.delay.default="" ', '', $html);
        //fi-btn
        return $html;
    }


}
