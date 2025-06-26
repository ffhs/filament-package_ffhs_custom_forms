// resources/js/drag_drop/get_values.js
function getAlpineData(element) {
  if (element._x_dataStack === void 0) return {};
  return Alpine.mergeProxies(element._x_dataStack);
}
function getGroup(element) {
  return getAlpineData(element).group ?? null;
}
function getElementKey(element) {
  let alpine = getAlpineData(element);
  if (alpine === null) return null;
  return alpine.element ?? null;
}
function isParent(element) {
  return getAlpineData(element).parent ?? false;
}
function getParent(element) {
  let currentParent = element;
  while (currentParent && !(currentParent instanceof Document)) {
    if (currentParent.hasAttribute("ffhs_drag:component")) {
      if (isParent(currentParent)) return currentParent;
    }
    currentParent = currentParent.parentNode;
  }
  return null;
}

// resources/js/drag_drop/move_elements.js
function flattenElementCheck(element, data) {
  let elementKey = getElementKey(element);
  if (elementKey === null) return false;
  let parentElement = getParent(element);
  let parentData = getAlpineData(parentElement);
  return parentData.statePath === data.statePath;
}
function countFlattenChildren(container, data) {
  let count = 0;
  container.querySelectorAll("[ffhs_drag\\:component]").forEach((element) => {
    if (!flattenElementCheck(element, data)) return;
    count++;
  });
  return count;
}
function updatePositionsFlatten(state, container, group, data) {
  let currentPos = 1;
  let dragDropPosAttribute = data.dragDropPosAttribute;
  let dragDropEndPosAttribute = data.dragDropEndPosAttribute;
  let usedKeys = [];
  container.querySelectorAll("[ffhs_drag\\:component]").forEach((element) => {
    if (!flattenElementCheck(element, data)) return;
    let elementKey = getElementKey(element);
    let contains = countFlattenChildren(element, data);
    if (state[elementKey] === void 0) state[elementKey] = {};
    usedKeys.push(elementKey);
    state[elementKey][dragDropPosAttribute] = currentPos;
    state[elementKey][dragDropEndPosAttribute] = contains === 0 ? null : currentPos + contains;
    currentPos++;
  });
  let notUsedKeys = Object.keys(state).filter((x) => !usedKeys.includes(x));
  notUsedKeys.forEach((x) => delete state[x]);
}
function updatePositionsOrder(state, container, group, data) {
  let currentPos = 1;
  let orderAttribute = data.orderAttribute;
  let parentContainer = getParent(container);
  container.querySelectorAll("[ffhs_drag\\:component]").forEach((element) => {
    let elementKey = getElementKey(element);
    if (!elementKey) return;
    if (getGroup(element) !== group) return;
    let parentElement = getParent(element);
    if (parentContainer !== parentElement) return;
    if (state[elementKey] === void 0) state[elementKey] = {};
    state[elementKey][orderAttribute] = currentPos;
    currentPos++;
  });
}
function updatePositions(state, container, group, parentData) {
  if (parentData.flatten) updatePositionsFlatten(state, container, group, parentData);
  else if (parentData.orderAttribute !== null) updatePositionsOrder(state, container, group, parentData);
}

// resources/js/drag_drop/alpine_components/container.js
function dragDropContainer(group) {
  return {
    group,
    container: true,
    element: null,
    parent: false,
    init() {
      Sortable.create(this.$el, {
        ghostClass: "drag-drop--element__ghost_effect",
        dragClass: "drag-drop--element__drag",
        group,
        animation: 150,
        forceFallback: true,
        handle: ".drag_drop_handle",
        onEnd: onEndClosure(group)
      });
    }
  };
}
function onEndClosure(group) {
  return function(evt) {
    let formParent = getParent(evt.from);
    let toParent = getParent(evt.to);
    let sameContainer = toParent === formParent;
    let toParentData = getAlpineData(toParent);
    let stateTo = toParentData.wire.get(toParentData.statePath, "");
    if (!stateTo || Array.isArray(stateTo)) stateTo = {};
    if (toParent.getAttribute("disabled")) return;
    if (formParent.getAttribute("disabled")) return;
    if (!sameContainer) {
      let formParentData = getAlpineData(formParent);
      let stateFrom = formParentData.wire.get(formParentData.statePath, "");
      if (!stateFrom || Array.isArray(stateFrom)) stateFrom = {};
      updatePositions(stateFrom, formParent, group, formParentData);
      formParentData.wire.set(formParentData.statePath, stateFrom, false);
    }
    updatePositions(stateTo, toParent, group, toParentData);
    let isLive = !sameContainer || toParentData.isLive;
    toParentData.wire.set(toParentData.statePath, stateTo, isLive);
  };
}
export {
  dragDropContainer as default,
  onEndClosure
};
