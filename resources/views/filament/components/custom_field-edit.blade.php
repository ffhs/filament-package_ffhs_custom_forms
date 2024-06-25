@php
    use Filament\Support\Facades\FilamentAsset;
    use function Filament\Support\prepare_inherited_attributes;
    $statePath = $getStatePath();
    $wireModel = 'wire:model' . ($isLive() ? '.live': '');
@endphp


<script
    src="{{FilamentAsset::getScriptSrc('custom_form_script',  'ffhs/filament-package_ffhs_custom_forms')}}"></script>

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <style>
        .custom-field-drag-over {
            border: 6px #1a202c;
            background-color: #f7f7ff !important;
        }
    </style>

    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('$statePath')" )}},
          }"
    >


        <x-filament::fieldset customField:form customField:has-fields class>
            <!--- toDo Setze wieder auf config coloums-->
            <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                 class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
                @foreach($field->getStructureState() as $key => $structure)
                    @include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field')
                @endforeach
            </div>
        </x-filament::fieldset>

    </div>

</x-dynamic-component>
