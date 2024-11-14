export function getAlpineData(element){
    if(element._x_dataStack === undefined) return {}
    return Alpine.mergeProxies(element._x_dataStack)
}


export function getGroup(element){
    return getAlpineData(element).group ?? null
}

export function getElementKey(element){
    let alpine = getAlpineData(element);
    if(alpine === null) return null;
    return alpine.element ?? null
}
export function isElement(element){
    let data = getAlpineData(element)
    return data.element !== null;
}

export function isParent(element){
    return getAlpineData(element).parent ?? false
}

export function isContainer(element){
    return getAlpineData(element).container ?? false
}

export function isDragcomponent(element){
    return getAlpineData(element).drag ?? false
}

export function isAction(element){
    return getAction(element) !== null
}


export function getAction(element){
    return getAlpineData(element).action ?? null
}


export function findDragElement() {
    return document.querySelector('[ffhs_drag\\:dragging]');
}

export function getParent(target) {
    let currentParent = target;

    while (currentParent && !(currentParent instanceof Document)) {
        if(currentParent.hasAttribute("ffhs_drag:component")){
            if(isParent(currentParent)) return currentParent;
        }
        currentParent = currentParent.parentNode;
    }
    return null;
}


export function hasSameGroup(elment1, elment2) {
    let dragGroup = getGroup(elment1);
    if(dragGroup === undefined) return false
    let targetGroup = getGroup(elment2);
    if(targetGroup === undefined) return false
    return dragGroup === targetGroup;
}

export function findTarget(target, search = (element) => isDragcomponent(element) || isContainer(element)){

    let currentParent = target;
    while (currentParent && !(currentParent instanceof Document)) {
        if(search(currentParent)) return currentParent
        currentParent = currentParent.parentNode;
    }

    return null
}
