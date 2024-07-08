
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
}*/

function countFlattenChildren(container, data, selector) {
    let count =0
    container.querySelectorAll(selector).forEach(element => {

        let parentElement = getParent(element)
        let parentData = Alpine.mergeProxies(parentElement._x_dataStack)
        if(parentData.statePath !== data.statePath) return

        count++
    })

    return count;
}


function updatePositionsFlatten(state, container, group, data) {
    let currentPos = 0;
    let selector = '[ffhs_drag\\:element][ffhs_drag\\:group="'+ group +'"]';

    let dragDropPosAttribute = data.dragDropPosAttribute
    let dragDropEndPosAttribute = data.dragDropEndPosAttribute


    container.querySelectorAll(selector).forEach(element => {

        let parentElement = getParent(element)
        let parentData = Alpine.mergeProxies(parentElement._x_dataStack)
        if(parentData.statePath !== data.statePath) return

        currentPos++

        let contains = countFlattenChildren(element, data, selector);
        let key = element.getAttribute('ffhs_drag:element')

        if (state[key] === undefined) state[key] = {}

        state[key][dragDropPosAttribute] = currentPos
        state[key][dragDropEndPosAttribute] = contains === 0 ? null : (currentPos + contains)
    })
}

function updatePositionsOrder(state, container, group, data) {
    let currentPos = 1;
    let selector = '[ffhs_drag\\:element][ffhs_drag\\:group="'+ group +'"]';

    let orderAttribute = data.orderAttribute;

    let parentContainer = getParent(container)

    container.querySelectorAll(selector).forEach(element => {
        //Check if is fields are from same Parent
        let parentElement = getParent(element)
        if(parentContainer !== parentElement) return


        let key = element.getAttribute('ffhs_drag:element')
        if (state[key] === undefined) state[key] = {}
        state[key][orderAttribute] = currentPos
        currentPos++
    })
}

function updatePositions(state, container, group, data) {
    if(data.flatten) updatePositionsFlatten(state, container, group, data)
    else if(data.orderAttribute !== null) updatePositionsOrder(state, container, group, data)
}


function updateLiveState(alpineData) {
    let isLive = alpineData.isLive
    if (!isLive) return false;
    let $wire = alpineData.wire
    $wire.$commit()
    return true;
}


function moveElementToOnOtherElement(target, toSet) {
    if (target.hasAttribute('ffhs_drag:element')) {
        target.before(toSet)
    }
    else if (target.hasAttribute('ffhs_drag:container')) {
        target.insertBefore(toSet, target.firstChild)
    }
}

function moveField(target, dragElement) {

    let targetParent = getParent(target)
    let sourceParent= getParent(dragElement)

    let sameContainer = sourceParent=== targetParent;

    let group = targetParent.getAttribute('ffhs_drag:group')


    let targetData = Alpine.mergeProxies(targetParent._x_dataStack)
    let sourceData = Alpine.mergeProxies(sourceParent._x_dataStack)


    let targetState = targetData.wire.get(targetData.statePath, '') //targetData.state
    let sourceState = sourceData.wire.get(sourceData.statePath, '') //sourceData.state

    //Fixing JavaScript bullish
    //if the object is empty it get back an proxy array back that fix this shit.
    if(Array.isArray(targetState)) targetState = {}
    if(Array.isArray(sourceState)) sourceState = {}


    moveElementToOnOtherElement(target, dragElement);

    if(!sameContainer){
        let dragKey = dragElement.getAttribute('ffhs_drag:element')

        //Move data to the other Field
        targetState[dragKey] = sourceState[dragKey];
        delete sourceState[dragKey];

        updatePositions(sourceState, sourceParent, group, sourceData)
        sourceData.wire.set(sourceData.statePath, sourceState)
        console.log(sourceState)
    }

    updatePositions(targetState, targetParent, group, targetData)

    targetData.wire.set(targetData.statePath, targetState)

    if(!sameContainer) targetData.wire.$commit()
    else updateLiveState(targetData);

}

function handleDrop(target) {

    let dragElement = findDragElement()

    if(dragElement == null) return;
    if(dragElement === target) return

    if(!hasSameGroup(dragElement,target)) return;

    moveField(target, dragElement);

    // commitState($wire)
}


function getParent(target) {
    let currentParent = target;

    while (currentParent && !(currentParent instanceof Document)) {
        if(currentParent.hasAttribute('ffhs_drag:parent')) return currentParent;
        currentParent = currentParent.parentNode;
    }

    return null;
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






