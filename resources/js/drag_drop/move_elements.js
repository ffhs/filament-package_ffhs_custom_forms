import {getAlpineData, getElementKey, getGroup, getParent, isContainer, isElement} from "./get_values.js";

export function moveElementToOnOtherElement(target, toSet) {
    if (isElement(target)) target.before(toSet)
    else if (isContainer(target)) target.prepend(toSet)

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

    let usedKeys = [];


    container.querySelectorAll('[ffhs_drag\\:component]').forEach(element => {

        if(!flattenElementCheck(element, data)) return;


        let elementKey = getElementKey(element);
        let contains = countFlattenChildren(element, data);

        if (state[elementKey] === undefined) state[elementKey] = {};

        usedKeys.push(elementKey);

        state[elementKey][dragDropPosAttribute] = currentPos;
        state[elementKey][dragDropEndPosAttribute] = contains === 0 ? null : (currentPos + contains);

        currentPos++
    })

     // Remove unused Keys
     let notUsedKeys = Object.keys(state).filter(x => !usedKeys.includes(x));
     notUsedKeys.forEach(x => delete state[x])
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

export function moveDraggable(target, dragElement) {

    let targetParent = getParent(target)
    let targetData = getAlpineData(targetParent)
    let targetState = targetData.wire.get(targetData.statePath, '') //targetData.state
    //Fixing JavaScript bullish
    //if the object is empty it get back an proxy array back that fix this shit.
    if(!targetState || Array.isArray(targetState)) targetState = {}

    let group = getGroup(targetParent)

    let sourceParent= getParent(dragElement)
    let sameContainer = sourceParent === targetParent;

    if(targetParent.getAttribute("disabled")) return;
    moveElementToOnOtherElement(target, dragElement);

    if(!sameContainer){
        let sourceData = getAlpineData(sourceParent)
        let sourceState = sourceData.wire.get(sourceData.statePath, '') //sourceData.state
        if (!sourceState || Array.isArray(sourceState)) sourceState = {}

        let dragKey = getElementKey(dragElement)

        //Move data to the other Field
        targetState[dragKey] = sourceState[dragKey];
        delete sourceState[dragKey];

        updatePositions(sourceState, sourceParent, group, sourceData)
        sourceData.wire.set(sourceData.statePath, sourceState, false)
    }


    updatePositions(targetState, targetParent, group, targetData)

    if(targetParent.getAttribute("disabled")) return;

    let isLive = (!sameContainer || targetData.isLive);
    targetData.wire.set(targetData.statePath, targetState, isLive)
}
