@php
    use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\HtmlString;
    use function Filament\Support\prepare_inherited_attributes;
    use Illuminate\View\ComponentAttributeBag;

    $type = $getType($fieldData);
    $icon = new HtmlString( "<span class='flex'>" . Blade::render('<x-'.$iconMap[$type]. "/>") . "<span style='padding-left: 10px;'>" . $typeNameMap[$type] . "</span></span>") ;
@endphp

<div draggable="true" customField:drag customField:uuid="{{$key}}" class="col-span-1">
    <x-filament::fieldset
        :label="$icon"
        :attributes="prepare_inherited_attributes(new ComponentAttributeBag())"
    >

        <div style="width: 20px; margin-left: 95%; margin-top: -20px; ">{{$getEditFieldActionContainer($key)}}</div>

        <span customField:property="type"> </span>

        <x-filament::input.wrapper
            style="width: 60%;  margin-top: -15px; margin-bottom: 10px;"
        >
            <input customField:property="name.{{app()->getLocale()}}"
                   style="width: 92%; margin-left: 5px"
                   class=" fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75
                   placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500
                   disabled:[-webkit-text-fill-color:theme(colors.gray.500)]
                   disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white
                   dark:placeholder:text-gray-500
                   dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)]
                   dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6"
            />
        </x-filament::input.wrapper>


        <div customField:uuid="front-options">
            {{$getFrontTypeOptions($key)}}
        </div>

        @if($field->hasFieldComponent($fieldData))
            @include($getFieldComponent($fieldData))
        @endif

    </x-filament::fieldset>
</div>
