@php
    use Filament\Support\Facades\FilamentAsset;
    /**@var Closure $getStatePath*/
    $statePath = $getStatePath();
    $stateKey = $getStatePath(false);
    $beforeState =explode(".". $stateKey, $statePath)[0];
@endphp


{{--<script src="{{FilamentAsset::getScriptSrc('drag_drop_script', 'ffhs/filament-package_ffhs_custom_forms')}}"></script>--}}


<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <div
        ax-load
        ax-load-src="{{FilamentAsset::getAlpineComponentSrc("drag_drop_parent", "ffhs/filament-package_ffhs_custom_forms")}}"
        x-ignore
        x-data="dragDropParent(
            '{{$statePath}}',
             '{{$stateKey}}',
             $wire,
             @js($isLive()),
             '{{$getNestedFlattenListType()::getPositionAttribute()}}',
             '{{$getNestedFlattenListType()::getEndContainerPositionAttribute()}}',
             @js($getOrderAttribute()),
             @js($isFlatten())
        )"

        ffhs_drag:parent
        ffhs_drag:group="{{$getDragDropGroup()}}"
    >


        <x-filament::fieldset
            class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6"
            style="
                --cols-default: repeat(2, minmax(0, 1fr)); --cols-lg: repeat({{$getGridSize()}}, minmax(0, 1fr));
                  background: rgba(200, 200, 200, {{$getDeepColor() * 0.1}})
            "

            ffhs_drag:container
            ffhs_drag:group="{{$getDragDropGroup()}}"
        >

                @foreach($getStructure() as $key => $structure)
                    <!-- ToDo FlattenList) -->
                    <!-- include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field') -->
                    @include('filament-package_ffhs_custom_forms::filament.components.drag-drop.element')

                @endforeach

        </x-filament::fieldset>

    </div>


</x-dynamic-component>
