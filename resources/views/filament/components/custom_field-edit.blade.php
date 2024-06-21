@php
    use function Filament\Support\prepare_inherited_attributes;
    $statePath = $getStatePath();
    $wireModel = 'wire:model' . ($isLive() ? '.live': '');
    $iconMap = $getIconMap();
    $typeNameMap = $getTypeNameMap();

@endphp


<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">


    <style>
        .custom-field-drag-over {
            border: 6px #1a202c;
            background-color: #f7f7ff !important;
        }
    </style>

    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('$statePath')" )}},
            statePath: '{{$statePath}}',
            wireModel: '{{$wireModel}}'
          }"

        x-init="
            let root = document.querySelector('[custom-form]')

            function findTarget(target){

                let currentParent = target;
                while (currentParent) {
                  if (currentParent.hasAttribute('customField:drag') || currentParent.hasAttribute('customField:has-fields')) {
                    break;
                  }
                  currentParent = currentParent.parentNode;
                }

                return currentParent
            }


            function setNestedValue(obj, path, value) {
              const pathArray = path.split('.');
              let current = obj;

              for (let i = 0; i < pathArray.length - 1; i++) {
                const key = pathArray[i];
                if (!current[key]) {
                  current[key] = {};
                }
                current = current[key];
              }

              current[pathArray[pathArray.length - 1]] = value;
            }

            function getNestedValue(obj, path) {
              const pathArray = path.split('.');
              let current = obj;

              for (let i = 0; i < pathArray.length; i++) {
                const key = pathArray[i];
                if (!current || !current.hasOwnProperty(key)) {
                  return null;
                }
                current = current[key];
              }

              return current;
            }


            function runForProperties(callable) {
                 root.querySelectorAll('[customField\\:property]').forEach(component => {
                    let key = null;

                    let currentParent = component;
                    while (!currentParent.hasAttribute('custom-form')) {
                            if(currentParent.hasAttribute('customField:uuid')){
                                if(key == null) key = currentParent.getAttribute('customField:uuid')
                                else key = currentParent.getAttribute('customField:uuid') + '.'+ key
                            }
                            currentParent = currentParent.parentNode;
                    }

                    key = 'custom_fields.'+key+ '.' + component.getAttribute('customField:property')
                    let staticKey = statePath+ '.' + key
                    callable(key, staticKey, component )
                 })
            }


            runForProperties((key, staticKey, component) => {
                 let value = getNestedValue(state, key);
                 if(value == null) component.setAttribute('value', ' ')
                 else component.setAttribute('value', value)
                 component.setAttribute(wireModel, staticKey)
            })

            root.querySelectorAll('[customField\\:drag]').forEach(fieldEl =>{
                fieldEl.addEventListener('dragstart', e => {
                    e.target.setAttribute('dragging',true)
                })


                fieldEl.addEventListener('drop', e => {
                    e.target.classList.remove('custom-field-drag-over')

                    let draggingEl = root.querySelector('[dragging]')

                    let target = findTarget(e.target)

                    if(draggingEl == target) return

                    if(target.hasAttribute('customField:drag')) target.before(draggingEl)
                    else target.insertBefore(draggingEl, target.firstChild)

                   let newState = {};

                    runForProperties((key, staticKey, component) => {
                        setNestedValue(newState, key, component.value)
                        component.setAttribute(wireModel, staticKey)
                    })

                    root.querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))

                    state = newState;

                })
                fieldEl.addEventListener('dragleave', e => {
                    let target = findTarget(e.target)
                    root.querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))
                })

                fieldEl.addEventListener('dragenter', e => {
                    e.preventDefault()
                    let dragLeaveTimeout = setTimeout(() => {
                        let target = findTarget(e.target)
                        target.classList.add('custom-field-drag-over')
                    },1)
                })


                fieldEl.addEventListener('dragend', e => {
                    e.target.removeAttribute('dragging')
                })
                fieldEl.addEventListener('dragover', e => e.preventDefault())
            })
        "
    >


        <x-filament::fieldset custom-form customField:has-fields class>
            <!--- toDo Setze wieder auf config-->
            <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));" class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
                @foreach($field->getState()['custom_fields'] as $key => $fieldData)
                    @include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field')
                @endforeach
            </div>
        </x-filament::fieldset>

    </div>

</x-dynamic-component>
