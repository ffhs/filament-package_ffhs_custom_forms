@php
    $isDisabled = $action->isDisabled();
    $url = $action->getUrl();
@endphp

<x-dynamic-component
    :color="$action->getColor()"
    component="filament::button"
    :disabled="$isDisabled"
    :form="$action->getFormToSubmit()"
    :form-id="$action->getFormId()"
    :href="$isDisabled ? null : $url"
    :icon="$action->getIcon()"
    :icon-size="$action->getIconSize()"
    :key-bindings="$action->getKeyBindings()"
    :label-sr-only="$action->isLabelHidden()"
    :tag="$url ? 'a' : 'button'"
    :target="($url && $action->shouldOpenUrlInNewTab()) ? '_blank' : null"
    :tooltip="$action->getTooltip()"
    :type="$action->canSubmitForm() ? 'submit' : 'button'"
    {{--    :wire:click="$action->getLivewireClickHandler()"--}}
    {{--    :wire:target="$action->getLivewireTarget()"--}}
    {{--    :x-on:click="$action->getAlpineClickHandler()"--}}
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->merge($action->getExtraAttributes(), escape: false)
            ->class(['fi-ac-action'])
    "

    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    :icon-position="$getIconPosition()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :outlined="$isOutlined()"
    :size="$getSize()"
    class="fi-ac-btn-action"
    :ffhs_drag:action="$action->getLivewireClickHandler()"
>
    {{ $getLabel() }}
</x-dynamic-component>
