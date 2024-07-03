@php
    use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
    use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\HtmlString;
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    /**@var CustomFieldType $type*/
    $type = $getFieldType($key);
    $generalField = $getGeneralField($key);


    if(is_null($generalField))
        $icon = Blade::render('<x-'.$type->icon() . " style='width: 25px;' />");
    else $icon = Blade::render('<x-'.$generalField->icon . " style='width: 20px;' />");

    $name = $type->getEditorFieldTitle($getState()[$key]);
    $label = new HtmlString("<span class='flex'>" . $icon .
            "<span draggable='true' style='padding-left: 10px; cursor: grab;'>" . $name . "</span></span>");

@endphp


<div customField:drag customField:uuid="{{$key}}"
     style="
  touch-action: pan-y;
        grid-column:  {{data_get($getState() ,   $key . '.options.new_line_option' )? " 1 /" : ""}} span {{data_get($getState() ,  $key . '.options.column_span' ) ?? 1}} !important;
     "
     x-init="setupField($el, state, $wire, '{{$statePath}}')"
>

    <x-filament::fieldset
        :label="$label"
        :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    >

        <div style="width: 50%; margin-left: 50%; margin-top: -20px; ">{{$getFieldActions($key)}}</div>


        <div style="width: 60%;  margin-top: -15px; margin-bottom: 10px;">
            {{$getFieldName($key)}}
        </div>

        @if(!is_null($getFieldComponent($key)) )
            @include($getFieldComponent($key))
        @endif

    </x-filament::fieldset>
</div>
