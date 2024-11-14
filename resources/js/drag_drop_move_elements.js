import {getAlpineData, getElementKey, getGroup, getParent, isContainer, isElement} from "./drag_drop_values.js";

export function moveElementToOnOtherElement(target, toSet) {
    if (isElement(target)) target.before(toSet)
    else if (isContainer(target)) target.insertBefore(toSet, target.firstChild)
}




function flattenElementCheck(element, data){
    let elementKey = getElementKey(element)

    if(elementKey === null) return false;

    let parentElement = getParent(element)
    let parentData = getAlpineData(parentElement)
    return parentData.statePath === data.statePath;
}

function countFlattenChildren(container, data) {
    let count =0
    container.querySelectorAll('[ffhs_drag\\:component]').forEach(element => {
        if(!flattenElementCheck(element, data)) return;
        count++
    })

    return count;
}



 function updatePositionsFlatten(state, container, group, data) {
    let currentPos = 1;
    let dragDropPosAttribute = data.dragDropPosAttribute
    let dragDropEndPosAttribute = data.dragDropEndPosAttribute

     let keySplit = crypto.randomUUID().split('-');
     let test =  keySplit[0] + keySplit[1];

     container.querySelectorAll('[ffhs_drag\\:component]').forEach(element => {
        console.log("updating " + test)
        if(!flattenElementCheck(element, data)) return;

        let elementKey = getElementKey(element)
        let contains = countFlattenChildren(element, data);

        if (state[elementKey] === undefined) state[elementKey] = {}

        state[elementKey][dragDropPosAttribute] = currentPos
        state[elementKey][dragDropEndPosAttribute] = contains === 0 ? null : (currentPos + contains)
        currentPos++
    })

}

function updatePositionsOrder(state, container, group, data) {
    let currentPos = 1;

    let orderAttribute = data.orderAttribute;

    let parentContainer = getParent(container)

    container.querySelectorAll('[ffhs_drag\\:component]').forEach(element => {

        let elementKey = getElementKey(element)
        if(!elementKey) return;
        if(getGroup(element) !== group) return;

        //Check if is fields are from same Parent
        let parentElement = getParent(element)
        if(parentContainer !== parentElement) return

        if (state[elementKey] === undefined) state[elementKey] = {}
        state[elementKey][orderAttribute] = currentPos
        currentPos++
    })
}


export function updatePositions(state, container, group, parentData) {
    if(parentData.flatten) updatePositionsFlatten(state, container, group, parentData)
    else if(parentData.orderAttribute !== null) updatePositionsOrder(state, container, group, parentData)
}
