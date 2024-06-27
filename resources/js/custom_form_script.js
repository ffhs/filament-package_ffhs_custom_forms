function findTarget(target, onlyDrag = false){

    let currentParent = target;
    while (currentParent && !(currentParent instanceof Document)) {

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

    while (!(currentParent instanceof Document) && !currentParent.hasAttribute('customField:form')) {
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

    let currentPos = 0;

    getParentForm().querySelectorAll('[customField\\:uuid]').forEach(element => {
        currentPos++

        let contains =  element.querySelectorAll('[customField\\:uuid]').length
        let key = element.getAttribute('customField:uuid')

        if(state[key] === undefined) return
        state[key]['form_position'] = currentPos
        state[key]['layout_end_position'] = contains === 0? null: (currentPos + contains)
    })

}

function handleNewField(target, draggingEl, $wire, staticPath) {
    let mode = draggingEl.getAttribute('customField:newFieldMode')
    let value = draggingEl.getAttribute('customField:newField')

    let targetId = "";
    let inId = "";
    if(target.hasAttribute('customField:hasFields')) {
        targetId = "";
        let inTarget = findTarget(target.parentNode, true)
        if(inTarget != null && !(inTarget instanceof Document))
            targetId = inTarget.getAttribute('customField:uuid')
    }
    else targetId = target.getAttribute('customField:uuid');


    $wire.mountFormComponentAction(staticPath,mode +'-create_field',
        {value:value, in:inId, before: targetId}
    );
}


function clearBackground() {
    getParentForm().classList.remove('custom-field-drag-over')
    getParentForm().querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))
}

function setupField(fieldEl, state, $wire, staticPath){
    setupDragField(fieldEl)

    fieldEl.addEventListener('drop', e => {
        e.stopPropagation();

        e.target.classList.remove('custom-field-drag-over')

        let target = findTarget(e.target)
        let draggingEl = document.querySelector('[customField\\:dragging]')
        if(draggingEl == null) return;
        if(draggingEl.hasAttribute('customField:drag')) handleDragDrop(target, draggingEl, state)
        if(draggingEl.hasAttribute('customField:newField')) handleNewField(target, draggingEl, $wire, staticPath)
    })

    fieldEl.addEventListener('dragleave', e => {
        e.stopPropagation();
        //let target = findTarget(e.target)
        clearBackground();
    })

    fieldEl.addEventListener('dragenter', e => {
        e.stopPropagation();
        e.preventDefault()
        setTimeout(() => {
            let target = findTarget(e.target)
            target.classList.add('custom-field-drag-over')
        },1)
    })

}


function setupDragField(fieldEl){

    fieldEl.addEventListener('drop', e => {
        e.stopPropagation();

        e.target.classList.remove('custom-field-drag-over')
    })

    fieldEl.addEventListener('dragstart', e => {
        e.stopPropagation();

        findTarget(e.target, true).setAttribute('customField:dragging',true)
    })

    fieldEl.addEventListener('dragend', e => {
        e.stopPropagation();

        findTarget(e.target, true).removeAttribute('customField:dragging')
    })

    fieldEl.addEventListener('dragover', e => e.preventDefault())
}

