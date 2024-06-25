@php
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;
    $structures = $structure;
    $saveKey = $key
@endphp

<x-filament::fieldset
    label="Fields"
    :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    style="
        --cols-default: repeat(1, minmax(0, 1fr));
        --cols-lg: repeat({{data_get($getState() ,  'data.' . $key . '.options.columns' ) ?? 1}} , minmax(0, 1fr));
        background: rgba(200, 200, 200, 0.1)
    "
    class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6"
    customField:hasFields="True"
>


    @foreach($structures ?? [] as $key => $structure)
        @include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field')
    @endforeach

</x-filament::fieldset>

@php
    $structure = $structures;
    $key =$saveKey
@endphp
