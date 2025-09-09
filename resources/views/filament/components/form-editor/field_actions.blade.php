@php
    use Filament\Support\Facades\FilamentAsset;
@endphp

<div
    x-load
    x-load-css="['{{FilamentAsset::getStyleHref('custom_forms', package: 'ffhs/filament-package_ffhs_drag-drop')}}']"
    class="-mt-5 -mr-1">
    @include('filament-schemas::components.actions')
</div>
