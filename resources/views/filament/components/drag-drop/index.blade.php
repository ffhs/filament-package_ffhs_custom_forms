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
            '{{$getDragDropGroup()}}',
            '{{$statePath}}',
             '{{$stateKey}}',
             $wire,
             @js($isLive()),
             '{{$getNestedFlattenListType()::getPositionAttribute()}}',
             '{{$getNestedFlattenListType()::getEndContainerPositionAttribute()}}',
             @js($getOrderAttribute()),
             @js($isFlatten())
        )"

{{--        wire:ignore.self--}}
        x-load-css="[@js(FilamentAsset::getStyleHref('drag_drop_css', package: 'ffhs/filament-package_ffhs_custom_forms'))]"
    >

        <x-filament::fieldset
            class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6 drag-drop__hover-effect"
            style="
                  --cols-default: repeat(2, minmax(0, 1fr));
                  --cols-lg: repeat({{$getGridSize()}}, minmax(0, 1fr));
                  background: rgba(200, 200, 200, {{$getDeepColor() * 0.1}})
            "

            ax-load
            ax-load-src="{{FilamentAsset::getAlpineComponentSrc('drag_drop_container', 'ffhs/filament-package_ffhs_custom_forms')}}"
            x-ignore
            x-data="dragDropContainer('{{$getDragDropGroup()}}')"
        >
                @foreach($getStructure() as $key => $structure)
                    @include('filament-package_ffhs_custom_forms::filament.components.drag-drop.element')
                @endforeach

        </x-filament::fieldset>

    </div>


</x-dynamic-component>
