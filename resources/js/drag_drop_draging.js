export function setupDraggable(elementField){
    elementField.addEventListener('dragstart', e => {
        e.stopPropagation();
        elementField.setAttribute('ffhs_drag:dragging',true)
    })

    elementField.addEventListener('dragend', e => {
        e.stopPropagation();
        elementField.removeAttribute('ffhs_drag:dragging')
    })

    elementField.addEventListener('dragover', e => e.preventDefault())
}
