@php use Filament\Support\Enums\VerticalAlignment; @endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :inline-label-vertical-alignment="VerticalAlignment::Center"
>

    {{ $getChildComponentContainer() }}

</x-dynamic-component>
