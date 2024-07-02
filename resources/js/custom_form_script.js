function findTarget(target, onlyDrag = false){

    let currentParent = target;
    while (currentParent && !(currentParent instanceof Document)) {

        if(!onlyDrag && currentParent.hasAttribute('customField:hasFields')) break;
        if (currentParent.hasAttribute('customField:drag'))  break;
        currentParent = currentParent.parentNode;
    }

    return currentParent
}

function getParentForm(){
    return document.querySelector('[customField\\:form]')
}

function updateFieldPositions(state) {
    let currentPos = 0;

    getParentForm().querySelectorAll('[customField\\:uuid]').forEach(element => {
        currentPos++


        let contains = element.querySelectorAll('[customField\\:uuid]').length
        let key = element.getAttribute('customField:uuid')

        if (state[key] === undefined) state[key] = {}

        state[key]['form_position'] = currentPos
        state[key]['layout_end_position'] = contains === 0 ? null : (currentPos + contains)
    })
}

function setFieldToOtherField(target, toSet) {
    if (target.hasAttribute('customField:drag')) target.before(toSet)
    else target.insertBefore(toSet, target.firstChild)
}

function handleDragDrop(target, draggingEl, state) {
    if(draggingEl === target) return

    setFieldToOtherField(target, draggingEl);

    getParentForm().querySelectorAll('*').forEach(element => element.classList.remove('custom-field-drag-over'))

    updateFieldPositions(state);

}

function handleNewField(target, draggingEl, state, $wire, staticPath) {

    let mode = draggingEl.getAttribute('customField:newFieldMode')
    let value = draggingEl.getAttribute('customField:newField')

    let child = document.createElement("span");

    let keySplit = crypto.randomUUID().split('-');
    let key = keySplit[0] + keySplit[1];
    child.setAttribute('customField:uuid', key)

    setFieldToOtherField(target, child);

    updateFieldPositions(state)
    let position = state[key]['form_position'];

    delete state[key]

    $wire.mountFormComponentAction(staticPath, mode +'-create_field',
        {value:value,  formPosition:  position}
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
        if(draggingEl.hasAttribute('customField:newField')) handleNewField(target, draggingEl,state, $wire, staticPath)
        else if(draggingEl.hasAttribute('customField:drag')) handleDragDrop(target, draggingEl, state)

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

