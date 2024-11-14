@php
    use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\HtmlString;
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;
    use Filament\Support\Facades\FilamentAsset;

    /**@var string $key*/
    /**@var Closure $getItemIcon*/
    /**@var Closure $getState*/
    /**@var Closure $getDragDropGroup*/
    /**@var Closure $getItemLabel*/
    /**@var Closure $getItemGridStart*/

    $icon = $getItemIcon($key);
    if(!empty($icon)) $icon = Blade::render('<x-'.$icon . " style='width: 20px;' />");

    $name = $getItemLabel($key);


    if(empty($name) && empty($getItemIcon($key))) $label = null;
    else $label = new HtmlString(
                "<span draggable='true' class='flex' style=' cursor: grab;'>" .
                (is_null($icon)? "" : $icon ."<span style='padding-left: 10px;'>" ).
                $name .
                (is_null($icon)? "" : "</span>" ).
                "</span>"
            );

    $gridColumn = $getItemGridStart($key);
    $gridColumn = $gridColumn . ($gridColumn ?" /": "");

@endphp


<div
    style="
        touch-action: pan-y;
        grid-column: {{$gridColumn}} span {{$getItemGridSize($key)}} !important;
     "

    ax-load
    ax-load-src="{{FilamentAsset::getAlpineComponentSrc("drag_drop_element", "ffhs/filament-package_ffhs_custom_forms")}}"
    x-ignore
    x-data="dragDropElement(@js($getDragDropGroup()), @js($key))"
    ffhs_drag:component
>

    <x-filament::fieldset
        :label="$label"
        class="drag-drop__hover-effect"
        :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    >

        <div class="drag-drop-list__element__action-container">
            {{$getItemActionContainer($key)}}
        </div>

        {{$getItemContainer($key)}}

        @if($isFlatten() && !$isFlattenViewHidden($key))
            @include($getFlattenView($key))
        @endif

    </x-filament::fieldset>

</div>
