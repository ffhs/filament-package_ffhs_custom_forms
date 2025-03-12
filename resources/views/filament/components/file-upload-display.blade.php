<div
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
    }}>

    <div style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);"
         class="flex justify-center fi-badge w-full gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary">
        <span
            style="flex: 1; text-align: left; color: black; padding-top: 3px; padding-left: 5px">{{$getLabel()}}</span>
        <span style="text-align: right; justify-items: right; padding-right: 5px">

            {{ $getChildComponentContainer() }}

        </span>
    </div>

</div>
