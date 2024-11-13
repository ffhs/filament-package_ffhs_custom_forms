import {
    findDragElement,
    getAlpineData,
    getElementKey,
    getGroup,
    getParent,
    hasSameGroup,
    isAction,
    isElement
} from "./drag_drop_values.js";
import {clearBackground} from "./drag_drop_hover_effect.js";
import {handleDropAction} from "./drag_drop_action_drop.js";
import {moveElementToOnOtherElement, updatePositions} from "./drag_drop_move_elements.js";


export function updateLiveState(alpineData) {
    let isLive = alpineData.isLive
    if (!isLive) return false;
    let $wire = alpineData.wire
    $wire.$commit()
    return true;
}




export function moveField(target, dragElement) {


    let targetParent = getParent(target)
    let sourceParent= getParent(dragElement)

    let sameContainer = sourceParent=== targetParent;

    let group = getGroup(targetParent)


    let targetData = getAlpineData(targetParent)
    let sourceData = getAlpineData(sourceParent)


    let targetState = targetData.wire.get(targetData.statePath, '') //targetData.state
    let sourceState = sourceData.wire.get(sourceData.statePath, '') //sourceData.state

    //Fixing JavaScript bullish
    //if the object is empty it get back an proxy array back that fix this shit.
    if(!targetState || Array.isArray(targetState)) targetState = {}
    if(!sourceState || Array.isArray(sourceState)) sourceState = {}


    moveElementToOnOtherElement(target, dragElement);

    if(!sameContainer){
        let dragKey = getElementKey(dragElement)

        //Move data to the other Field
        targetState[dragKey] = sourceState[dragKey];
        delete sourceState[dragKey];

        updatePositions(sourceState, sourceParent, group, sourceData)
        sourceData.wire.set(sourceData.statePath, sourceState)
    }

    updatePositions(targetState, targetParent, group, targetData)

    targetData.wire.set(targetData.statePath, targetState)

    if(!sameContainer) targetData.wire.$commit()
    else updateLiveState(targetData);

}




export function handleDrop(target) {

    let dragElement = findDragElement()

    if(dragElement == null) return;
    if(dragElement === target) return

    if(!hasSameGroup(dragElement,target)) return;

    if(isAction(dragElement)) handleDropAction(target, dragElement);

    else moveField(target, dragElement);
}












export function setUpDropField(element){

    //ToDo Somtimes the element is registert twice
    if(isElement(element)) console.log(getElementKey(element))

    element.addEventListener('drop', e => {
        // it called multiple times
        e.stopPropagation();
        e.preventDefault()

        console.log("drop")
        handleDrop(element)
        clearBackground()
    })
}






















