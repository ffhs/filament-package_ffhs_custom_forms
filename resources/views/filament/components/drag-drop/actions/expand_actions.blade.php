@php
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

    <div
        class="expand-action--group fi-btn fi-fo-component-ctn"
         x-data="{ open: false }"
    >

        <button class="expand-action--expand-action"
                type="button" @click="open = !open">
{{--            ToDo Translate--}}
            <span x-show="!open">Öffnen </span>
            <span x-show="open">Schliessen </span>
        </button>


    @foreach($getOptions() as $id => $label)

            @php
                $actionPath = $getActionsPath().".".$getName()."-".$id."Action','".$getName()."-".$id;
                $mountAction = "mountFormComponentAction('".$actionPath."')";
            @endphp

            <span
                x-show="open"

                @if($isOptionDisabled($id, $label))
                    disabled
                @else
                    draggable="true"
                    ax-load
                    ax-load-src="{{FilamentAsset::getAlpineComponentSrc("action", "ffhs/filament-package_ffhs_drag-drop")}}"
                    x-ignore
                    x-data="dragDropAction(@js($getDragDropGroup()), @js($mountAction))"
                    ffhs_drag:component
                @endif

                class="expand-action--action"
            >
                 <span>{{$label}}</span>
            </span>

        @endforeach
    </div>

</x-dynamic-component>
