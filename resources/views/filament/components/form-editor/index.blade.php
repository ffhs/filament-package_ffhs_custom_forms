@php use Filament\Support\Facades\FilamentAsset; @endphp


<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    class="items-stretch"
>
    <span class="hidden"
          x-load
          x-load-css="['{{FilamentAsset::getStyleHref('custom_forms', package: 'ffhs/filament-package_ffhs_custom-forms')}}']"
    > </span>
    {{ $getChildSchema() }}
</x-dynamic-component>
