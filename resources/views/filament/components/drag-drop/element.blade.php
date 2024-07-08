@php
    use Illuminate\Support\HtmlString;
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    /**@var string $key*/
    /**@var Closure $getItemIcon*/
    /**@var Closure $getState*/
    /**@var Closure $getDragDropGroup*/
    /**@var Closure $getItemLabel*/

    $icon = $getItemIcon($key);
    if(!is_null($icon)) $icon = Blade::render('<x-'.$icon . " style='width: 20px;' />");

    $name = $getItemLabel($key);

    $label = new HtmlString(
                "<span draggable='true' class='flex' style=' cursor: grab;'>" .
                (is_null($icon)? "" : $icon ."<span style='padding-left: 10px;'>" ).
                $name .
                (is_null($icon)? "" : "</span>" ).
                "</span>"
            );
@endphp


<div
    ffhs_drag:group="{{$getDragDropGroup()}}"
    ffhs_drag:element="{{$key}}"
    ffhs_drag:drag

    style="
        touch-action: pan-y;

        grid-column:  {{$getItemGridStart($key)?? ""}} / span {{$getItemGridSize($key)}} !important;
     "
    x-init="setupDomElement($el)"
>

    <x-filament::fieldset
        :label="$label"

        :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    >

        <div  style="width: 50%; margin-left: 50%; margin-top: -20px; margin-bottom: 10px">
            {{$getItemActionContainer($key)}}
        </div>

        {{$getItemContainer($key)}}

        @if($isFlatten() && !$isFlattenViewHidden($key))
            @include($getFlattenView($key))
        @endif

    </x-filament::fieldset>

</div>
