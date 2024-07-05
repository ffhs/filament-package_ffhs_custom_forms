@php
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    $structureField = $structure
@endphp

<x-filament::fieldset
    :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"

    style="margin-top: 20px"

    ffhs_drag:container
    ffhs_drag:group="{{$getDragDropGroup()}}"

>

    @foreach($structureField as $key => $structure)
        <!-- ToDo FlattenList) -->
        <!-- include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field') -->
        @include('filament-package_ffhs_custom_forms::filament.components.drag-drop.element')

    @endforeach

</x-filament::fieldset>
