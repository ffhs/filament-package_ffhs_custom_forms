@php
    use Filament\Support\Facades\FilamentAsset;
    /**@var Closure $getStatePath*/
    $statePath = $getStatePath();
@endphp


<script src="{{FilamentAsset::getScriptSrc('drag_drop_script', 'ffhs/filament-package_ffhs_custom_forms')}}"> </script>


<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <style>
        .ffhs-drag-over {
            border: 6px #1a202c;
            background-color: #f7f7ff !important;
        }
    </style>

        <x-filament::fieldset

        >

            <div


                class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6"
                style="--cols-default: repeat(2, minmax(0, 1fr)); --cols-lg: repeat({{$getGridSize()}}, minmax(0, 1fr));"

                x-init="setupDomElement($el);"
                x-data="{
                    statePath: '{{$statePath}}',
                    wire: $wire,
                    isLive: @js($isLive()),
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('$statePath')" )}},
                    dragDropPosAttribute: '{{$getNestedFlattenListType()::getPositionAttribute()}}',
                    dragDropEndPosAttribute: '{{$getNestedFlattenListType()::getEndContainerPositionAttribute()}}',
                }"

                ffhs_drag:container
                ffhs_drag:group="{{$getDragDropGroup()}}"


            >

                @foreach($getStructure() as $key => $structure)  <!-- ToDo FlattenList) -->
                   <!-- include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field') -->
                    @include('filament-package_ffhs_custom_forms::drag-drop-element')

                @endforeach

            </div>


        </x-filament::fieldset>


</x-dynamic-component>
