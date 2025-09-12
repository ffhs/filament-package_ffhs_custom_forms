<span class="hidden"
      x-load
      x-load-css="{{\Filament\Support\Facades\FilamentAsset::getStyleHref('custom_forms', 'ffhs/filament-package_ffhs_custom-forms')}}"
> </span>

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    class="items-stretch"
>
    {{ $getChildSchema() }}
</x-dynamic-component>
