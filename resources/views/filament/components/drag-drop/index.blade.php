@php
    use Filament\Support\Facades\FilamentAsset;

    /**@var Closure $getStatePath*/
    $statePath = $getStatePath();
    $stateKey = $getStatePath(false);
    $beforeState =explode(".". $stateKey, $statePath)[0];
@endphp


{{--<script src="{{FilamentAsset::getScriptSrc('drag_drop_script', 'ffhs/filament-package_ffhs_drag-drop')}}"></script>--}}


<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <div
        ax-load
        ax-load-src="{{FilamentAsset::getAlpineComponentSrc("parent", "ffhs/filament-package_ffhs_drag-drop")}}"
        x-ignore
{{--        {{$applyStateBindingModifiers('wire:model.defer')}}="{{$statePath}}"--}}

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

        x-load-css="[@js(FilamentAsset::getStyleHref('stylesheet', package: 'ffhs/filament-package_ffhs_drag-drop'))]"
        wire:loading.class="opacity-50"
        wire:loading.attr="disabled"
        wire:target="mountFormComponentAction, {{$statePath}}"
        ffhs_drag:component
    >

        @php
            $structure = $getStructure();
            $key = null;
            $getFlattenGrid = function ($key) use ($getGridSize) {
                return $getGridSize();
            };
        @endphp

        @include('filament-package_ffhs_custom_forms::filament.components.drag-drop.default-container')

{{--        <x-filament::fieldset--}}
{{--            class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6 drag-drop__hover-effect"--}}
{{--            style="--}}
{{--                  --cols-default: repeat(2, minmax(0, 1fr));--}}
{{--                  --cols-lg: repeat({{$getGridSize()}}, minmax(0, 1fr));--}}
{{--                  background: rgba(200, 200, 200, {{$getDeepColor() * 0.1}})--}}
{{--            "--}}

{{--            ax-load--}}
{{--            ax-load-src="{{FilamentAsset::getAlpineComponentSrc('container', 'ffhs/filament-package_ffhs_drag-drop')}}"--}}
{{--            x-ignore--}}
{{--            x-data="dragDropContainer('{{$getDragDropGroup()}}')"--}}
{{--        >--}}
{{--                @foreach($getStructure() as $key => $structure)--}}
{{--                    @include('filament-package_ffhs_custom_forms::filament.components.drag-drop.element')--}}
{{--                @endforeach--}}

{{--        </x-filament::fieldset>--}}

    </div>


</x-dynamic-component>
