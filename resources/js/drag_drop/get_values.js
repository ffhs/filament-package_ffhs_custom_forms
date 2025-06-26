export function getAlpineData(element) {
    if (element._x_dataStack === undefined) return {}
    return Alpine.mergeProxies(element._x_dataStack)
}

export function getGroup(element) {
    return getAlpineData(element).group ?? null
}

export function getElementKey(element) {
    let alpine = getAlpineData(element);
    if (alpine === null) return null;
    return alpine.element ?? null
}

export function isParent(element) {
    return getAlpineData(element).parent ?? false
}

export function getParent(element) {
    let currentParent = element;

    while (currentParent && !(currentParent instanceof Document)) {
        if (currentParent.hasAttribute("ffhs_drag:component")) {
            if (isParent(currentParent)) return currentParent;
        }
        currentParent = currentParent.parentNode;
    }
    return null;
}
