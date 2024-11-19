@php
    use Filament\Support\Facades\FilamentAsset;
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    $structureField = $structure;
    $keyOld = $key;

    if(isset($getFlattenGrid))  $flattenGrid = $getFlattenGrid($key);
    else $flattenGrid = $getGridSize();

    if(isset($getFlattenViewLabel)) $label = $getFlattenViewLabel($key);
    else $label = "";

@endphp

<x-filament::fieldset
    :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    :label="$label"

    class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6 drag-drop__hover-effect "

    style="
        --cols-default: repeat(1, minmax(0, 1fr));
        --cols-lg: repeat({{$flattenGrid}}, minmax(0, 1fr));
        margin-top: 20px;
        background: rgba(200, 200, 200, 0.1)
    "

    ax-load
    ax-load-src="{{FilamentAsset::getAlpineComponentSrc('container', 'ffhs/filament-package_ffhs_drag-drop')}}"
    x-ignore
    x-data="dragDropContainer('{{$getDragDropGroup()}}')"
    ffhs_drag:component
>

    @foreach($structureField as $key => $structure)
        @include('filament-package_ffhs_custom_forms::filament.components.drag-drop.element')
    @endforeach

</x-filament::fieldset>

@php
    $structure = $structureField;
    $key = $keyOld;
@endphp
