@php
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament::fieldset
    label="Fields"
    :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    customField:has-fields
    customField:uuid="custom_fields"
    style="background: rgba(200, 200, 200, 0.1)"
>

    @foreach($fieldData['custom_fields'] ?? [] as $key => $fieldData)
        @include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field')
    @endforeach

</x-filament::fieldset>
