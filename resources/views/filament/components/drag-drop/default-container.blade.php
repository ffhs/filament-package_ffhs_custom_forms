@php
    use Barryvdh\Debugbar\Facades\Debugbar;use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    $structureField = $structure;
    $keyOld = $key;
@endphp

<x-filament::fieldset
    :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    :label="$getFlattenViewLabel($key)"

    class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6"

    style="
        margin-top: 20px;
        --cols-default: repeat(2, minmax(0, 1fr)); --cols-lg: repeat({{$getFlattenGrid($key)}}, minmax(0, 1fr));
        background: rgba(200, 200, 200, 0.1)
    "

    ffhs_drag:container
    ffhs_drag:group="{{$getDragDropGroup()}}"

>

    @foreach($structureField as $key => $structure)
        @include('filament-package_ffhs_custom_forms::filament.components.drag-drop.element')

    @endforeach

</x-filament::fieldset>

@php
    $structure = $structureField;
    $key = $keyOld;
@endphp
