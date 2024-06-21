@php
    use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\HtmlString;
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    //$fieldData = $getFieldDataState()[$key];

    /**@var CustomFieldType $type*/
    $type = $getFieldType($key);
    $icon = new HtmlString( "<span class='flex'>" . Blade::render('<x-'.$type->icon(). "/>") . "<span style='padding-left: 10px;'>" . $type->getTranslatedName() . "</span></span>") ;
@endphp

<div draggable="true" customField:drag customField:uuid="{{$key}}" class="col-span-1">
    <x-filament::fieldset
        :label="$icon"
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
