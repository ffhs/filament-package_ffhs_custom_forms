@php
    use Filament\Support\Facades\FilamentAsset;use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\HtmlString;
    use function Filament\Support\prepare_inherited_attributes;

    $statePath = $getStatePath();
    $wireModel = 'wire:model' . ($isLive() ? '.live': '');
@endphp






<x-dynamic-component :component="$getFieldWrapperView()">

    <legend class="-ms-2 px-2 text-sm font-medium leading-6 text-gray-950 dark:text-white">
        {{$getLabel()}}
    </legend>

    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
         class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-4">

        @foreach($getTypes() as $type)

            <span
                draggable="true"
                customField:newFieldMode="type"
                customField:newField="{{$type->identifier()}}"

                x-init="setupDragField($el)"

                style="
                --c-400:var(--primary-400);
                --c-500:var(--primary-500);
                --c-600:var(--primary-600);

                 width: 100%;
                height: 60px;"

                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none
                 transition duration-75

                 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary
                 fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid fi-btn-outlined
                 ring-1 text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500
                 fi-ac-action fi-ac-btn-action"
            >

                    <div class="flex flex-col items-center justify-center">
                        {{new HtmlString(Blade::render("<x-".$type->icon() . ' class="w-6 h-6" />'))}}
                        <span style="margin-top: 2px;  text-align: center;"> {{$type->getTranslatedName()}} </span>
                    </div>

            </span>

        @endforeach
    </div>


</x-dynamic-component>
