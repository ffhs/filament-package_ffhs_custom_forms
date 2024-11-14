import registerEvent from "./drag_drop_events.js";
import {getParent} from "./drag_drop_values.js";

export function setupDraggable(element){
    registerEvent("dragstart", element, event =>{
        event.stopPropagation();

        if(getParent(element).getAttribute("disabled")) return;
        element.setAttribute('ffhs_drag:dragging',true)
    })

    element.addEventListener('dragend', e => {
        e.stopPropagation();
        element.removeAttribute('ffhs_drag:dragging')
    })

    element.addEventListener('dragover', e => e.preventDefault())
}
