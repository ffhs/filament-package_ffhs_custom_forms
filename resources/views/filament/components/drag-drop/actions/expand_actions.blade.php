@php
    use Illuminate\Support\HtmlString;
    use Filament\Support\Facades\FilamentAsset;


    $rgbToHex = function ($rgbString) {
        list($r, $g, $b) = explode(',', $rgbString);
        $r = trim($r);
        $g = trim($g);
        $b = trim($b);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    };
@endphp
<x-dynamic-component :component="$getFieldWrapperView()"
                     style="
                        margin-top: -35px;
                        --expand-drag-drop-action--color-500: {{$getColor()[500]}};


                        --expand-drag-drop-action--color-200: {{$rgbToHex($getColor()[200])}};
                        --expand-drag-drop-action--color-900: {{$rgbToHex($getColor()[900])}};
                        --expand-drag-drop-action--color-950: {{$rgbToHex($getColor()[950])}};
                        --expand-drag-drop-action--color-50: {{$rgbToHex($getColor()[50])}};
                     ">

    <legend class="-ms-2 px-2 text-sm font-medium leading-6 text-gray-950 dark:text-white" style="padding-top: 20px; padding-bottom: -30px">
        {{$getLabel()}}
    </legend>


    <div style="--cols-default: repeat(1, minmax(0, 1fr)); margin-top: -4px"
         class="fi-btn expand-drag-drop-action grid grid-cols-[--cols-default] fi-fo-component-ctn gap-4 bg-white dark:bg-gray-800">

        @foreach($getOptions() as $id => $label)

            @php
                $actionPath = $getActionsPath().".".$getName()."-".$id."Action','".$getName()."-".$id;
                $mountAction = "mountFormComponentAction('".$actionPath."')";
            @endphp

            <span
                @if(!$isOptionDisabled($id, $label))
                    draggable="true"

                    ax-load
                    ax-load-src="{{FilamentAsset::getAlpineComponentSrc("action", "ffhs/filament-package_ffhs_drag-drop")}}"
                    x-ignore
                    x-data="dragDropAction(@js($getDragDropGroup()), @js($mountAction))"
                    ffhs_drag:component
{{--                    x-init="setupDraggable($el)"--}}
                @endif



                class="
                    h-8
                    font-semibold
                    transition duration-75
                    w-full

                    fi-size-md fi-btn-size-md gap-1.5 px-3 py-2
                    text-xs

                    @if($isOptionDisabled($id, $label))
                        expand-drag-drop-action-disabled
                    @else
                        expand-drag-drop-action-option
                    @endif
                    "
            >
                 {{$label}}
                </span>

        @endforeach
    </div>

</x-dynamic-component>
