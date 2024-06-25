@php
    use function Filament\Support\prepare_inherited_attributes;
    $statePath = $getStatePath();
    $wireModel = 'wire:model' . ($isLive() ? '.live': '');
@endphp

<script src="{{FilamentAsset::getScriptSrc('custom_form_script',  'ffhs/filament-package_ffhs_custom_forms')}}"></script>

<script>
    function initCustomForm(state, $wire){

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


        function getElementFieldStructurePath(element) {
            let currentParent = element.parentNode
            let path = element.getAttribute('customField:uuid')

            while (!currentParent.hasAttribute('customField:form')) {
                if (currentParent.hasAttribute('customField:uuid')){
                    path = currentParent.getAttribute('customField:uuid') + '.' + path
                }
                currentParent = currentParent.parentNode
            }
            return path;
        }

        function handleDragDrop(target, draggingEl) {
            if(draggingEl == target) return

            if(target.hasAttribute('customField:drag')) target.before(draggingEl)
            else target.insertBefore(draggingEl, target.firstChild)

            root.querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))

            let structure = {};

            root.querySelectorAll('[customField\\:uuid]').forEach(element => {
                let path = getElementFieldStructurePath(element)

                let value = getNestedValue(structure,path)
                if(value == null) value = {};
                setNestedValue(structure, path, value)
            })

            let newState = state
            newState['structure'] = structure
            state = newState;
        }


        function handleNewField(target, draggingEl) {
            let mode = draggingEl.getAttribute('customField:newFieldMode')
            let value = draggingEl.getAttribute('customField:newField')

            let path = getElementFieldStructurePath(target)

            let targetId = target.getAttribute('customField:uuid');

            $wire.mountFormComponentAction('data.custom_fields','createField',
                {mode:mode, value:value, path:path.replace('.' + targetId,''), before: targetId}
            );
        }


        let root = document.querySelector('[customField\\:form]')
        root.querySelectorAll('[customField\\:drag]').forEach(fieldEl =>{
            fieldEl.addEventListener('drop', e => {
                e.stopPropagation();
                e.target.classList.remove('custom-field-drag-over')

                let target = findTarget(e.target)
                let draggingEl = document.querySelector('[customField\\:dragging]')
                if(draggingEl == null) return;
                if(draggingEl.hasAttribute('customField:drag')) handleDragDrop(target, draggingEl)
                if(draggingEl.hasAttribute('customField:newField')) handleNewField(target, draggingEl)
            })

            fieldEl.addEventListener('dragleave', e => {
                //let target = findTarget(e.target)
                root.querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))
            })

            fieldEl.addEventListener('dragenter', e => {
                e.preventDefault()
                let dragLeaveTimeout = setTimeout(() => {
                    let target = findTarget(e.target)
                    target.classList.add('custom-field-drag-over')
                },1)
            })
        })

        document.querySelectorAll('[customField\\:drag], [customField\\:newField]').forEach(fieldEl =>{
            fieldEl.addEventListener('dragstart', e => {
                e.target.setAttribute('customField:dragging',true)
            })

            fieldEl.addEventListener('dragend', e => {
                e.target.removeAttribute('customField:dragging')
            })

            fieldEl.addEventListener('dragover', e => e.preventDefault())
        })
    }
</script>


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
          }"

        x-init="initCustomForm(state, $wire)"
    >


        <x-filament::fieldset customField:form customField:has-fields class>
            <!--- toDo Setze wieder auf config coloums-->
            <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));" class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
                @foreach($field->getStructureState() as $key => $structure)
                    @include('filament-package_ffhs_custom_forms::custom_form_edit.custom-field')
                @endforeach
            </div>
        </x-filament::fieldset>

    </div>

</x-dynamic-component>
