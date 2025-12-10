<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div style="width: 100% !important; padding-top: 25px">
        <x-filament::badge color="success" style="width: 100% !important; display: block !important;">
            <div style="position: relative; width: 100% !important; min-height: 24px; display: block !important;">
                <div style="position: absolute; left: 5px; top: 50%; transform: translateY(-50%);">
                    {{$getLabel()}}
                </div>
                <div style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%);">
                    {{ $getChildSchema() }}
                </div>
            </div>
        </x-filament::badge>
    </div>
</x-dynamic-component>
