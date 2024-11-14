import {findDragElement, getElementKey, getParent, hasSameGroup, isContainer} from "./get_values.js";
import registerEvent from "./register_events.js";

function dragenterEvent(element, event) {
    let dragElement = findDragElement()
    if (dragElement == null) return

    if (!hasSameGroup(dragElement, element)) return
    if(getParent(element)?.getAttribute("disabled"))  return;


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
