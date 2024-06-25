function findTarget(target, onlyDrag = false){

    let currentParent = target;
    while (currentParent) {

        if(!onlyDrag && currentParent.hasAttribute('customField:hasFields')) break;
        if (currentParent.hasAttribute('customField:drag'))  break;
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


function getParentForm(){
    return  document.querySelector('[customField\\:form]')
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

function handleDragDrop(target, draggingEl, state) {
    if(draggingEl === target) return

    if(target.hasAttribute('customField:drag')) target.before(draggingEl)
    else target.insertBefore(draggingEl, target.firstChild)

    getParentForm().querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))

    let structure = {};

    getParentForm().querySelectorAll('[customField\\:uuid]').forEach(element => {
        let path = getElementFieldStructurePath(element)

        let value = getNestedValue(structure,path)
        if(value == null) value = {};
        setNestedValue(structure, path, value)
    })

    let newState = state
    newState['structure'] = structure
    state = newState;
}


function handleNewField(target, draggingEl, $wire) {
    let mode = draggingEl.getAttribute('customField:newFieldMode')
    let value = draggingEl.getAttribute('customField:newField')

    let path = getElementFieldStructurePath(target)

    let targetId = target.getAttribute('customField:uuid');

    $wire.mountFormComponentAction('data.custom_fields','createField',
        {mode:mode, value:value, path:path.replace('.' + targetId,''), before: targetId}
    );
}


function setupField(fieldEl, state, $wire){
    setupDragField(fieldEl)

    fieldEl.addEventListener('drop', e => {
        e.stopPropagation();

        e.target.classList.remove('custom-field-drag-over')

        let target = findTarget(e.target)
        let draggingEl = document.querySelector('[customField\\:dragging]')
        if(draggingEl == null) return;
        if(draggingEl.hasAttribute('customField:drag')) handleDragDrop(target, draggingEl, state)
        if(draggingEl.hasAttribute('customField:newField')) handleNewField(target, draggingEl, $wire)
    })

    fieldEl.addEventListener('dragleave', e => {
        //let target = findTarget(e.target)
        getParentForm().querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))
    })

    fieldEl.addEventListener('dragenter', e => {
        e.preventDefault()
        setTimeout(() => {
            let target = findTarget(e.target)
            target.classList.add('custom-field-drag-over')
        },1)
    })

}


function setupDragField(fieldEl){
    fieldEl.addEventListener('dragstart', e => {
        findTarget(e.target, true).setAttribute('customField:dragging',true)
    })

    fieldEl.addEventListener('dragend', e => {
        findTarget(e.target, true).removeAttribute('customField:dragging')
    })

    fieldEl.addEventListener('dragover', e => e.preventDefault())
}

