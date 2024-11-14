import {
    findDragElement,
    getAlpineData,
    getElementKey,
    getGroup,
    getParent,
    hasSameGroup,
    isAction
} from "./drag_drop_values.js";
import {clearBackground} from "./drag_drop_hover_effect.js";
import {handleDropAction} from "./drag_drop_action_drop.js";
import {moveElementToOnOtherElement, updatePositions} from "./drag_drop_move_elements.js";
import registerEvent from "./drag_drop_events.js";


function updateLivewirePostion(sourceParent, dragElement, targetState, group) {
    let sourceData = getAlpineData(sourceParent)
    let sourceState = sourceData.wire.get(sourceData.statePath, '') //sourceData.state
    if (!sourceState || Array.isArray(sourceState)) sourceState = {}

    let dragKey = getElementKey(dragElement)

    //Move data to the other Field
    targetState[dragKey] = sourceState[dragKey];
    delete sourceState[dragKey];

    updatePositions(sourceState, sourceParent, group, sourceData)
    sourceData.wire.set(sourceData.statePath, sourceState)
}

export function moveField(target, dragElement) {

    let targetParent = getParent(target)
    let targetData = getAlpineData(targetParent)
    let targetState = targetData.wire.get(targetData.statePath, '') //targetData.state
    //Fixing JavaScript bullish
    //if the object is empty it get back an proxy array back that fix this shit.
    if(!targetState || Array.isArray(targetState)) targetState = {}

    let group = getGroup(targetParent)

    let sourceParent= getParent(dragElement)
    let sameContainer = sourceParent=== targetParent;

    if(targetParent.getAttribute("disabled")) return;
    moveElementToOnOtherElement(target, dragElement);

    if(!sameContainer){
        updateLivewirePostion(sourceParent, dragElement, targetState, group);
    }


    updatePositions(targetState, targetParent, group, targetData)

    if(targetParent.getAttribute("disabled")) return;

    let isLive = (!sameContainer || targetData.isLive);
    targetData.wire.set(targetData.statePath, targetState, isLive)
}













export function setUpDropField(element){
    registerEvent("drop", element, event =>{
        event.stopPropagation();
        event.preventDefault();

        let dragElement = findDragElement()

        if(dragElement == null) return;
        if(dragElement === element) return;

        if(!hasSameGroup(dragElement,element)) return;
        if(getParent(element).getAttribute("disabled")) return;

        if(isAction(dragElement)) handleDropAction(element, dragElement);

        else moveField(element, dragElement);

        clearBackground();
    })
}






















