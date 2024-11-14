import {
    findTarget,
    getAction,
    getAlpineData,
    getElementKey,
    getGroup,
    getParent,
    isElement
} from "./drag_drop_values.js";
import {moveElementToOnOtherElement, updatePositions} from "./drag_drop_move_elements.js";


function createTemporaryChild(group, key, target) {

    let temporaryChild = document.createElement("span");

    temporaryChild.setAttribute('x-data', `dragDropElement('${group}','${key}')`)
    temporaryChild.setAttribute("ffhs_drag:component", null)
    temporaryChild.classList.add("hidden")
    moveElementToOnOtherElement(target, temporaryChild);
    Alpine.initTree(temporaryChild);

    return temporaryChild;
}

function generateElementKey() {
    let keySplit = crypto.randomUUID().split('-');
    return keySplit[0] + keySplit[1];
}


function findPosition(isFlatten, state, key, targetData) {
    if (isFlatten) return state[key][targetData.dragDropPosAttribute];
    else return state[key][targetData.orderAttribute];
}

export function handleDropAction(target, dragElement) {
    let targetParent = getParent(target)
    let group = getGroup(targetParent)

    let targetParentData = getAlpineData(targetParent)
    let isFlatten = targetParentData.flatten
    let $wire = targetParentData.wire
    let targetState = $wire.get(targetParentData.statePath, '')
    if(Array.isArray(targetState)) targetState = {}

    if(targetParent.getAttribute("disabled")) return;

    let targetId = getElementKey(target);

    //Prepare stuff
    let temporaryKey = generateElementKey();
    let temporaryChild = createTemporaryChild(group, temporaryKey, target);

    // Clone State to find position without updating the real state
    let cloneState = JSON.parse(JSON.stringify(targetState))
    updatePositions(cloneState, targetParent, group ,targetParentData)

    //position
    let position = findPosition(isFlatten, cloneState, temporaryKey, targetParentData);

    //parent element of Target element (target is in an container)
    let targetIn = null
    let targetInId = null
    if(isFlatten) targetIn = findTarget(temporaryChild.parentNode, (element) => isElement(element))
    if(targetIn) targetInId = getElementKey(targetIn)


    //run Action
    let action = getAction(dragElement)

    let toActionPath = action.split("'")[1]
    let toDoAction = action.split("'")[3]


    if(targetParent.getAttribute("disabled")) return;

    let metaData =  {targetPath:targetParentData.statePath,  position:position, flatten:isFlatten, targetIn:targetInId, target:targetId};

    $wire.mountFormComponentAction(toActionPath, toDoAction, metaData);
}
