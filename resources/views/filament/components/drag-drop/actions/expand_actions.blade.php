@php
    use Illuminate\Support\HtmlString;

    $styleClass = "field-adder-". uniqid();
    $styleOptionClass = "field-adder-option-". uniqid();

    $rgbToHex = function ($rgbString) {
        list($r, $g, $b) = explode(',', $rgbString);
        $r = trim($r);
        $g = trim($g);
        $b = trim($b);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    };
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" style="margin-top: -35px">

    <legend class="-ms-2 px-2 text-sm font-medium leading-6 text-gray-950 dark:text-white" style="padding-top: 20px; padding-bottom: -30px">
        {{$getLabel()}}
    </legend>


    <style>
        .{{$styleClass}}{
            width: 100%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid rgb({{$getColor()[500]}});
            border-radius: 10px;
            padding: 7px;
            text-align: center;
            transition: height 0.3s ease;
            margin-top: 20px;
            overflow: hidden;
            height: 50px;
            font-size: 20px;
        }

        .{{$styleClass}}:hover {
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            height: 100%;
        }


        .{{$styleOptionClass}}:hover {
            background-color: rgb({{$getColor()[100]}});
        }

    </style>

    <div style="--cols-default: repeat(1, minmax(0, 1fr)); margin-top: -4px"
         class="fi-btn {{$styleClass}} grid grid-cols-[--cols-default] fi-fo-component-ctn gap-4 bg-white dark:bg-gray-800">

        @foreach($getOptions() as $id => $label)
            <span
                @if(!$isOptionDisabled($id, $label))
                    draggable="true"
                    ffhs_drag:drag
                    ffhs_drag:group="{{$getDragDropGroup()}}"
                    ffhs_drag:action="mountFormComponentAction('{{$getActionsPath()}}.{{$getName()}}-{{$id}}Action','{{$getName()}}-{{$id}}')"
{{--                    x-init="setupDraggable($el)"--}}
                @endif


                style="
                    --c-400:var(rgb({{$getColor()[400]}}));
                    --c-500:var(rgb({{$getColor()[500]}}));
                    --c-600:var(rgb({{$getColor()[600]}}));

                     width: 100%;
                    height: 30px;"

                class="
                    font-semibold
                    transition duration-75


                     fi-size-md fi-btn-size-md gap-1.5 px-3 py-2
                     text-xs
                     @if($isOptionDisabled($id, $label))
                         bg-[{{$rgbToHex($getColor()[50])}}]
                         dark:bg-[{{$rgbToHex($getColor()[950])}}]
                     @else
                         hover:bg-[{{$rgbToHex($getColor()[100])}}]
                         dark:hover:bg-[{{$rgbToHex($getColor()[900])}}]
                     @endif
                     "
            >
                      {{new HtmlString($label)}}

                </span>

        @endforeach
    </div>

</x-dynamic-component>
