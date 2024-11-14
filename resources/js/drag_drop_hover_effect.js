import {findDragElement, getElementKey, hasSameGroup, isContainer} from "./drag_drop_values.js";

function dragenterEvent(element, event) {
    let dragElement = findDragElement()
    if (dragElement == null) return

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
    element.addEventListener('dragenter', event => dragenterEvent(element, event))
    element.addEventListener('dragleave', event => dragleaveEvent(event))
}

export function clearBackground() {
    document.querySelectorAll('*').forEach(element => {
        element.removeAttribute("ffhs_drag:hower_over")
    })
}
