import {getAlpineData, getElementKey, getGroup, getParent} from "./get_values.js";

function flattenElementCheck(element, data) {
    let elementKey = getElementKey(element)
    if (elementKey === null) return false;

    let parentElement = getParent(element)
    let parentData = getAlpineData(parentElement)
    return parentData.statePath === data.statePath;
}

function countFlattenChildren(container, data) {
    let count = 0
    container.querySelectorAll('[ffhs_drag\\:component]').forEach(element => {
        if (!flattenElementCheck(element, data)) return;
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
        if (!flattenElementCheck(element, data)) return;

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
        if (!elementKey) return;
        if (getGroup(element) !== group) return;

        //Check if is fields are from same Parent
        let parentElement = getParent(element)
        if (parentContainer !== parentElement) return

        if (state[elementKey] === undefined) state[elementKey] = {}
        state[elementKey][orderAttribute] = currentPos
        currentPos++
    })
}


export function updatePositions(state, container, group, parentData) {
    if (parentData.flatten) updatePositionsFlatten(state, container, group, parentData)
    else if (parentData.orderAttribute !== null) updatePositionsOrder(state, container, group, parentData)
}
