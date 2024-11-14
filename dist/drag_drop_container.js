// resources/js/drag_drop_values.js
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
function isElement(element) {
  let data = getAlpineData(element);
  return data.element !== null;
}
function isParent(element) {
  return getAlpineData(element).parent ?? false;
}
function isContainer(element) {
  return getAlpineData(element).container ?? false;
}
function isDragcomponent(element) {
  return getAlpineData(element).drag ?? false;
}
function isAction(element) {
  return getAction(element) !== null;
}
function getAction(element) {
  return getAlpineData(element).action ?? null;
}
function findDragElement() {
  return document.querySelector("[ffhs_drag\\:dragging]");
}
function getParent(target) {
  let currentParent = target;
  while (currentParent && !(currentParent instanceof Document)) {
    if (currentParent.hasAttribute("ffhs_drag:component")) {
      if (isParent(currentParent)) return currentParent;
    }
    currentParent = currentParent.parentNode;
  }
  return null;
}
function hasSameGroup(elment1, elment2) {
  let dragGroup = getGroup(elment1);
  if (dragGroup === void 0) return false;
  let targetGroup = getGroup(elment2);
  if (targetGroup === void 0) return false;
  return dragGroup === targetGroup;
}
function findTarget(target, search = (element) => isDragcomponent(element) || isContainer(element)) {
  let currentParent = target;
  while (currentParent && !(currentParent instanceof Document)) {
    if (search(currentParent)) return currentParent;
    currentParent = currentParent.parentNode;
  }
  return null;
}

// resources/js/drag_drop_events.js
function registerEvent(eventName, element, event) {
  if (!element[`__has_${eventName}`]) {
    const handler = (e) => event(e);
    element.addEventListener(eventName, handler);
    element[`__has_${eventName}`] = true;
  }
}

// resources/js/drag_drop_hover_effect.js
function dragenterEvent(element, event) {
  let dragElement = findDragElement();
  if (dragElement == null) return;
  if (getParent(element).getAttribute("disabled")) return;
  if (!hasSameGroup(dragElement, element)) return;
  event.stopPropagation();
  event.preventDefault();
  if (!isContainer(element) && getElementKey(element) === getElementKey(dragElement)) {
    return;
  }
  setTimeout(() => {
    element.setAttribute("ffhs_drag:hower_over", true);
  }, 0);
}
function dragleaveEvent(event) {
  let dragElement = findDragElement();
  if (dragElement == null) return;
  event.preventDefault();
  event.stopPropagation();
  clearBackground();
}
function setupDragOverEffect(element) {
  registerEvent("dragenter", element, (event) => dragenterEvent(element, event));
  registerEvent("dragleave", element, (event) => dragleaveEvent(event));
}
function clearBackground() {
  document.querySelectorAll("*").forEach((element) => {
    element.removeAttribute("ffhs_drag:hower_over");
  });
}

// resources/js/drag_drop_move_elements.js
function moveElementToOnOtherElement(target, toSet) {
  if (isElement(target)) target.before(toSet);
  else if (isContainer(target)) target.insertBefore(toSet, target.firstChild);
}
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
  let keySplit = crypto.randomUUID().split("-");
  let test = keySplit[0] + keySplit[1];
  container.querySelectorAll("[ffhs_drag\\:component]").forEach((element) => {
    console.log("updating " + test);
    if (!flattenElementCheck(element, data)) return;
    let elementKey = getElementKey(element);
    let contains = countFlattenChildren(element, data);
    if (state[elementKey] === void 0) state[elementKey] = {};
    state[elementKey][dragDropPosAttribute] = currentPos;
    state[elementKey][dragDropEndPosAttribute] = contains === 0 ? null : currentPos + contains;
    currentPos++;
  });
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

// resources/js/drag_drop_action_drop.js
function createTemporaryChild(group, key, target) {
  let temporaryChild = document.createElement("span");
  temporaryChild.setAttribute("x-data", `dragDropElement('${group}','${key}')`);
  temporaryChild.setAttribute("ffhs_drag:component", null);
  temporaryChild.classList.add("hidden");
  moveElementToOnOtherElement(target, temporaryChild);
  Alpine.initTree(temporaryChild);
  return temporaryChild;
}
function generateElementKey() {
  let keySplit = crypto.randomUUID().split("-");
  return keySplit[0] + keySplit[1];
}
function findPosition(isFlatten, state, key, targetData) {
  if (isFlatten) return state[key][targetData.dragDropPosAttribute];
  else return state[key][targetData.orderAttribute];
}
function handleDropAction(target, dragElement) {
  let targetParent = getParent(target);
  let group = getGroup(targetParent);
  let targetParentData = getAlpineData(targetParent);
  let isFlatten = targetParentData.flatten;
  let $wire = targetParentData.wire;
  let targetState = $wire.get(targetParentData.statePath, "");
  if (Array.isArray(targetState)) targetState = {};
  if (targetParent.getAttribute("disabled")) return;
  let targetId = getElementKey(target);
  let temporaryKey = generateElementKey();
  let temporaryChild = createTemporaryChild(group, temporaryKey, target);
  let cloneState = JSON.parse(JSON.stringify(targetState));
  updatePositions(cloneState, targetParent, group, targetParentData);
  let position = findPosition(isFlatten, cloneState, temporaryKey, targetParentData);
  let targetIn = null;
  let targetInId = null;
  if (isFlatten) targetIn = findTarget(temporaryChild.parentNode, (element) => isElement(element));
  if (targetIn) targetInId = getElementKey(targetIn);
  let action = getAction(dragElement);
  let toActionPath = action.split("'")[1];
  let toDoAction = action.split("'")[3];
  if (targetParent.getAttribute("disabled")) return;
  let metaData = { targetPath: targetParentData.statePath, position, flatten: isFlatten, targetIn: targetInId, target: targetId };
  $wire.mountFormComponentAction(toActionPath, toDoAction, metaData);
}

// resources/js/drag_drop_dropping.js
function updateLivewirePostion(sourceParent, dragElement, targetState, group) {
  let sourceData = getAlpineData(sourceParent);
  let sourceState = sourceData.wire.get(sourceData.statePath, "");
  if (!sourceState || Array.isArray(sourceState)) sourceState = {};
  let dragKey = getElementKey(dragElement);
  targetState[dragKey] = sourceState[dragKey];
  delete sourceState[dragKey];
  updatePositions(sourceState, sourceParent, group, sourceData);
  sourceData.wire.set(sourceData.statePath, sourceState);
}
function moveField(target, dragElement) {
  let targetParent = getParent(target);
  let targetData = getAlpineData(targetParent);
  let targetState = targetData.wire.get(targetData.statePath, "");
  if (!targetState || Array.isArray(targetState)) targetState = {};
  let group = getGroup(targetParent);
  let sourceParent = getParent(dragElement);
  let sameContainer = sourceParent === targetParent;
  if (targetParent.getAttribute("disabled")) return;
  moveElementToOnOtherElement(target, dragElement);
  if (!sameContainer) {
    updateLivewirePostion(sourceParent, dragElement, targetState, group);
  }
  updatePositions(targetState, targetParent, group, targetData);
  if (targetParent.getAttribute("disabled")) return;
  let isLive = !sameContainer || targetData.isLive;
  targetData.wire.set(targetData.statePath, targetState, isLive);
}
function setUpDropField(element) {
  registerEvent("drop", element, (event) => {
    event.stopPropagation();
    event.preventDefault();
    let dragElement = findDragElement();
    if (dragElement == null) return;
    if (dragElement === element) return;
    if (!hasSameGroup(dragElement, element)) return;
    if (getParent(element).getAttribute("disabled")) return;
    if (isAction(dragElement)) handleDropAction(element, dragElement);
    else moveField(element, dragElement);
    clearBackground();
  });
}

// resources/js/alpine_components/drag_drop_container.js
function dragDropContainer(group) {
  return {
    group,
    container: true,
    action: null,
    element: null,
    parent: false,
    drag: false,
    init() {
      setupDragOverEffect(this.$el);
      setUpDropField(this.$el);
    }
  };
}
export {
  dragDropContainer as default
};
