import {findDragElement, getParent, hasSameGroup, isAction} from "./get_values.js";
import {clearBackground} from "./hover_effect.js";
import {handleDropAction} from "./action_drop.js";
import registerEvent from "./register_events.js";
import {moveDraggable} from "./move_elements.js";


export function setUpDropField(element){
    registerEvent("drop", element, event =>{
        event.stopPropagation();
        event.preventDefault();

        let dragElement = findDragElement()

        if(dragElement == null) return;
        if(dragElement === element) return;

        if(!hasSameGroup(dragElement,element)) return;
        if(getParent(element).getAttribute("disabled")) return;

        clearBackground();

        if(isAction(dragElement)) handleDropAction(element, dragElement);
        else moveDraggable(element, dragElement);


    })
}






















