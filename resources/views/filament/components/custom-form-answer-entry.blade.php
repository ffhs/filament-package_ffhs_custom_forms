<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    {{ $getChildSchema() }}
</x-dynamic-component>
