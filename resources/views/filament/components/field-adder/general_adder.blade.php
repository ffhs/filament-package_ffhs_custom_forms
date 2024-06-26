@php
    $statePath = $getStatePath();
    $wireModel = 'wire:model' . ($isLive() ? '.live': '');
@endphp


<x-dynamic-component :component="$getFieldWrapperView()">

    <legend class="-ms-2 px-2 text-sm font-medium leading-6 text-gray-950 dark:text-white">
        {{$getLabel()}}
    </legend>


    <style>
        .card {
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #329dbd;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            transition: height 0.3s ease;
            margin-top: 20px;
            overflow: hidden;
            height: 50px;
            font-size: 20px;
        }

        .card:hover {
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            height: 100%;
        }


        .general-field:hover{
            background-color: #cdf4ff;
        }

        .disabled-general-field{
            color: #828f93;
        }
    </style>

   <div style="--cols-default: repeat(1, minmax(0, 1fr)); margin-top: -5px"
        class="fi-btn card grid grid-cols-[--cols-default] fi-fo-component-ctn gap-4">

        @foreach($getGeneralFieldSelectOptions() as $id => $name)
                <span
                    @if(!$isGeneralDisabled($id))
                    draggable="true"
                    customField:newFieldMode="general"
                    customField:newField="{{$id}}"
                    customField:drag="True"
                    @endif

                    x-init="setupDragField($el)"

                    style="
                    --c-400:var(--primary-400);
                    --c-500:var(--primary-500);
                    --c-600:var(--primary-600);

                     width: 100%;
                    height: 30px;"

                    class="
                    font-semibold
                    transition duration-75


                     fi-size-md fi-btn-size-md gap-1.5 px-3 py-2
                     text-xs

                    @if(!$isGeneralDisabled($id))
                     general-field
                    @endif

                    @if($isGeneralDisabled($id))
                     disabled-general-field
                    @endif

                     "
                >
                    <span>Bla</span>{{$name}}

                </span>

        @endforeach
    </div>


</x-dynamic-component>
