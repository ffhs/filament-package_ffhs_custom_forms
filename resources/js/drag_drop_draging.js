import registerEvent from "./drag_drop_events.js";

export function setupDraggable(element){
    registerEvent("dragstart", element, event =>{
        event.stopPropagation();
        element.setAttribute('ffhs_drag:dragging',true)
    })

    element.addEventListener('dragend', e => {
        e.stopPropagation();
        element.removeAttribute('ffhs_drag:dragging')
    })

    element.addEventListener('dragover', e => e.preventDefault())
}
