@php
    use Filament\Support\Facades\FilamentAsset;
@endphp

<span
    ax-load
    ax-load-src="{{FilamentAsset::getAlpineComponentSrc("action_group", "ffhs/filament-package_ffhs_drag-drop")}}"
    x-ignore
    x-data="dragDropActionGroup(@js($getDragDropGroup()))"
    class="drag-drop--action-container flex"
>
    @foreach ($getActions() as $action)
        @if ($action->isVisible())
            {{ $action }}
        @endif
    @endforeach

</span>
