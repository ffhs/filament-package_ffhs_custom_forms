@php
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;
    $structures = $structure;
    $saveKey = $key
@endphp

<x-filament::fieldset
    label="Fields"
    :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    style="background: rgba(200, 200, 200, 0.1)"
>

    @foreach($structures ?? [] as $key => $structure)
        @include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field')
    @endforeach

</x-filament::fieldset>

@php
    $structure = $structures;
    $key =$saveKey
@endphp
