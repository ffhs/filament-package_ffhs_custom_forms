import registerEvent from "./register_events.js";
import {getParent} from "./get_values.js";

export function setupDraggable(element){
    registerEvent("dragstart", element, event =>{
        event.dataTransfer.setData('text/plain', null); // Required for Firefox
        event.stopPropagation();

        let parent = getParent(element);
        if(parent !== null && parent.getAttribute("disabled")) return;
        element.setAttribute('ffhs_drag:dragging',true)
    })

    element.addEventListener('dragend', e => {
        e.stopPropagation();
        element.removeAttribute('ffhs_drag:dragging')
    })
}
