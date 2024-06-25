@php
    use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
    use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\HtmlString;
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    //$fieldData = $getFieldDataState()[$key];

    /**@var CustomFieldType $type*/
    $type = $getFieldType($key);
    $label = new HtmlString( "<span class='flex'>" . Blade::render('<x-'.$type->icon(). "/>") .
        "<span draggable='true' style='padding-left: 10px; cursor: grab;'>" . $type->getEditorFieldTitle($getFieldDataState()[$key]) . "</span></span>"
    );
@endphp

    <!--

        grid-column: span  / 1 !important;



-->

<div  customField:drag customField:uuid="{{$key}}"
     style="
        grid-column:  {{data_get($getState() ,  'data.' . $key . '.options.new_line_option' )? " 1 /" : ""}} span {{data_get($getState() ,  'data.' . $key . '.options.column_span' ) ?? 1}} !important;
     "
     x-init="setupField($el, state, $wire)"
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
