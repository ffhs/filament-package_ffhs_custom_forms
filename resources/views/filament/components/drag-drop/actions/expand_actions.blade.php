@php
    use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
    use Filament\Support\Facades\FilamentAsset;

    $rgbToHex = function ($rgbString) {
        [$r, $g, $b] = explode(',', $rgbString);
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
        x-data="{ expended: false }"
        wire:ignore
    >
        <button class="expand-action--expand-action"
                type="button" @click="expended = !expended">
            {{--            ToDo Translate--}}
            <span x-show="!expended" x-cloak>
                {{ CustomForms::__('drag_and_drop.actions.expand_action.open') }}
            </span>
            <span x-show="expended" x-cloak>
                {{ CustomForms::__('drag_and_drop.actions.expand_action.close') }}
            </span>
        </button>
        <span
            class=""
            x-show="expended" x-cloak
            ax-load
            ax-load-src="{{ FilamentAsset::getAlpineComponentSrc('action_group', 'ffhs/filament-package_ffhs_drag-drop') }}"
            x-ignore
            x-data="dragDropActionGroup(@js($getDragDropGroup(), ))"
        >
        @foreach($getOptions() as $id => $label)
                @php
                    $actionPath = $getActionsPath() . '.' . $getName() . '-' . $id . "Action','" . $getName() . '-' . $id;
                    $mountAction = "mountFormComponentAction('" . $actionPath . "')";
                @endphp

                <span
                    @if($isOptionDisabled($id, $label))
                        disabled
                    aria-disabled="true"
                    class="expand-action--action disabled-drag_drop"
                    @else
                        ffhs_drag:action="{{ $mountAction }}"
                    class="expand-action--action"
                    @endif
                >
                    <span>{{ $label }}</span>
                </span>
            @endforeach
        </span>
    </div>
</x-dynamic-component>
