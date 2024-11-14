import {findDragElement, getElementKey, getParent, hasSameGroup, isContainer} from "./drag_drop_values.js";
import registerEvent from "./drag_drop_events.js";

function dragenterEvent(element, event) {
    let dragElement = findDragElement()
    if (dragElement == null) return

    if(getParent(element).getAttribute("disabled")) return;
    if (!hasSameGroup(dragElement, element)) return


    event.stopPropagation()
    event.preventDefault()

    if(!isContainer(element) && getElementKey(element) === getElementKey(dragElement)) {
        return
    }

    setTimeout(() => {
        element.setAttribute("ffhs_drag:hower_over", true)
    }, 0)
}

function dragleaveEvent(event) {
    let dragElement = findDragElement()
    if (dragElement == null) return

    event.preventDefault()
    event.stopPropagation()
    clearBackground()
}

export function setupDragOverEffect(element){
    registerEvent('dragenter',element, event => dragenterEvent(element, event))
    registerEvent('dragleave',element, event => dragleaveEvent(event))
}

export function clearBackground() {
    document.querySelectorAll('*').forEach(element => {
        element.removeAttribute("ffhs_drag:hower_over")
    })
}
