

function setFieldToOtherField(target, toSet) {
    if(!hasSameGroup(target, toSet)) return

    if (target.hasAttribute('ffhs_drag:element')){
        target.before(toSet)
    }

    else if (target.hasAttribute('ffhs_drag:container')){
        target.insertBefore(toSet, target.firstChild)
    }
}










/*
function handleNewField(target, draggingEl, state, $wire, staticPath) {

    let mode = draggingEl.getAttribute('customField:newFieldMode')
    let value = draggingEl.getAttribute('customField:newField')

    let child = document.createElement("span");

    let keySplit = crypto.randomUUID().split('-');
    let key = keySplit[0] + keySplit[1];
    child.setAttribute('customField:uuid', key)

    setFieldToOtherField(target, child);

    let cloneState = JSON.parse(JSON.stringify(state))

    updatePositions(cloneState)
    let position = cloneState[key]['form_position'];

    $wire.mountFormComponentAction(staticPath, mode +'-create_field',
        {value:value,  formPosition:  position}
    );
}



function setupField(fieldEl, state, $wire, staticPath){
    setupDraggable(fieldEl)

    fieldEl.addEventListener('drop', e => {
        e.stopPropagation();

        e.target.classList.remove('custom-field-drag-over')

        let target = findTarget(e.target)
        let draggingEl = document.querySelector('[customField\\:dragging]')
        if(draggingEl == null) return;
        if(draggingEl.hasAttribute('customField:newField')) handleNewField(target, draggingEl,state, $wire, staticPath)
        else if(draggingEl.hasAttribute('customField:drag')) handleDragDrop(target, draggingEl, state)

    })



}*/







function updatePositions(state, container, group, dragDropPosAttribute, dragDropEndPosAttribute) {
    let currentPos = 0;
    let selector = '[ffhs_drag\\:element][ffhs_drag\\:group="'+ group +'"]';

    container.querySelectorAll(selector).forEach(element => {
        currentPos++

        let contains = element.querySelectorAll(selector).length
        let key = element.getAttribute('customField:uuid')

        if (state[key] === undefined) state[key] = {}

        state[key][dragDropPosAttribute] = currentPos
        state[key][dragDropEndPosAttribute] = contains === 0 ? null : (currentPos + contains)
    })
}

function handleDrop(target) {

    let dragElement = findDragElement()

    if(dragElement == null) return;

    let uppersContainer= getUppersContainer(target)

    if(!uppersContainer) return
    if(dragElement === target) return
    if(dragElement === uppersContainer) return

    let group = uppersContainer.getAttribute('ffhs_drag:group')

    let alpineData = Alpine.mergeProxies(uppersContainer._x_dataStack)

    let state = alpineData.state
    let dragDropPosAttribute = alpineData.dragDropPosAttribute
    let dragDropEndPosAttribute = alpineData.dragDropEndPosAttribute


    setFieldToOtherField(target, dragElement)

    updatePositions(state, uppersContainer,group ,  dragDropPosAttribute, dragDropEndPosAttribute)
}





function getUppersContainer(target) {
    let highest = null

    let currentParent = target;

    while (currentParent && !(currentParent instanceof Document)) {
        if(currentParent.hasAttribute('ffhs_drag:container') && hasSameGroup(target,currentParent))
            highest = currentParent
        currentParent = currentParent.parentNode;
    }

    return highest;
}



function findTarget(target, attributes = ['ffhs_drag:container', 'ffhs_drag:drag']){

    let currentParent = target;
    while (currentParent && !(currentParent instanceof Document)) {

        for (const attribute of attributes)
            if(currentParent.hasAttribute(attribute)) return currentParent

        currentParent = currentParent.parentNode;
    }

    return null
}

function findDragElement() {
    return document.querySelector('[ffhs_drag\\:dragging]');
}


function clearBackground() {
    //ToDo fix flackern
    document.querySelectorAll('*').forEach(element => element.classList.remove('ffhs-drag-over'))
}

function hasSameGroup(elment1, elment2) {
    let dragGroup = elment1.getAttribute('ffhs_drag:group');
    let targetGroup = elment2.getAttribute('ffhs_drag:group');
    return dragGroup === targetGroup;
}



function setupDragOverEffect(element){

    element.addEventListener('dragenter', e => {
        let dragElement = findDragElement()
        if(dragElement == null) return
        if(!dragElement.hasAttribute('ffhs_drag:group')) return

        let target = findTarget(e.target)

        if(!target) return;
        if(!hasSameGroup(dragElement,target)) return;


        e.stopPropagation();
        e.preventDefault()

        setTimeout(() => {
            target.classList.add('ffhs-drag-over')
        },0)
    })


    element.addEventListener('dragleave', e => {

        let dragElement = findDragElement()
        if(dragElement == null) return
        if(!dragElement.hasAttribute('ffhs_drag:group')) return

        let target = findTarget(e.target)

        if(!target) return;
        if(!hasSameGroup(dragElement,target)) return;

        e.stopPropagation();
        clearBackground();
    })

}

function setupDraggable(fieldEl){

    fieldEl.addEventListener('dragstart', e => {
        e.stopPropagation();

        let target = findTarget(e.target, ['ffhs_drag:drag']);

        target.setAttribute('ffhs_drag:dragging',true)
    })

    fieldEl.addEventListener('dragend', e => {
        e.stopPropagation();


        findTarget(e.target, ['ffhs_drag:drag'])
            .removeAttribute('ffhs_drag:dragging')
    })

    fieldEl.addEventListener('dragover', e => e.preventDefault())
}


function setUpDropField(element){

    element.addEventListener('drop', e => {
        e.stopPropagation();

        clearBackground()

        let target = findTarget(e.target)
        handleDrop(target)

        /* if(draggingEl.hasAttribute('customField:newField')) handleNewField(target, draggingEl,state, $wire, staticPath)
         else if(draggingEl.hasAttribute('customField:drag')) */

    })
}

function setupDomElement(element){
    setupDraggable(element)
    setupDragOverEffect(element)
    setUpDropField(element)
}






