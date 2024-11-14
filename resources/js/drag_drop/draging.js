import registerEvent from "./register_events.js";
import {getParent} from "./get_values.js";

export function setupDraggable(element){
    registerEvent("dragstart", element, event =>{
        event.stopPropagation();

        let parent = getParent(element);
        if(parent !== null && parent.getAttribute("disabled")) return;
        element.setAttribute('ffhs_drag:dragging',true)
    })

    element.addEventListener('dragend', e => {
        e.stopPropagation();
        element.removeAttribute('ffhs_drag:dragging')
    })

    element.addEventListener('dragover', e => e.preventDefault())
}
