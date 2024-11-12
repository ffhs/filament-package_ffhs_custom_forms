// resources/js/drag_drop_script.js
function countFlattenChildren(container, data, selector) {
  let count = 0;
  container.querySelectorAll(selector).forEach((element) => {
    let parentElement = getParent(element);
    let parentData = Alpine.mergeProxies(parentElement._x_dataStack);
    if (parentData.statePath !== data.statePath) return;
    count++;
  });
  return count;
}
function updatePositionsFlatten(state, container, group, data) {
  let currentPos = 0;
  let selector = '[ffhs_drag\\:element][ffhs_drag\\:group="' + group + '"]';
  let dragDropPosAttribute = data.dragDropPosAttribute;
  let dragDropEndPosAttribute = data.dragDropEndPosAttribute;
  container.querySelectorAll(selector).forEach((element) => {
    let parentElement = getParent(element);
    let parentData = Alpine.mergeProxies(parentElement._x_dataStack);
    if (parentData.statePath !== data.statePath) return;
    currentPos++;
    let contains = countFlattenChildren(element, data, selector);
    let key = element.getAttribute("ffhs_drag:element");
    if (state[key] === void 0) state[key] = {};
    state[key][dragDropPosAttribute] = currentPos;
    state[key][dragDropEndPosAttribute] = contains === 0 ? null : currentPos + contains;
  });
}
function updatePositionsOrder(state, container, group, data) {
  let currentPos = 1;
  let selector = '[ffhs_drag\\:element][ffhs_drag\\:group="' + group + '"]';
  let orderAttribute = data.orderAttribute;
  let parentContainer = getParent(container);
  container.querySelectorAll(selector).forEach((element) => {
    let parentElement = getParent(element);
    if (parentContainer !== parentElement) return;
    let key = element.getAttribute("ffhs_drag:element");
    if (state[key] === void 0) state[key] = {};
    state[key][orderAttribute] = currentPos;
    currentPos++;
  });
}
function updatePositions(state, container, group, data) {
  if (data.flatten) updatePositionsFlatten(state, container, group, data);
  else if (data.orderAttribute !== null) updatePositionsOrder(state, container, group, data);
}
function updateLiveState(alpineData) {
  let isLive = alpineData.isLive;
  if (!isLive) return false;
  let $wire = alpineData.wire;
  $wire.$commit();
  return true;
}
function moveElementToOnOtherElement(target, toSet) {
  if (target.hasAttribute("ffhs_drag:element")) {
    target.before(toSet);
  } else if (target.hasAttribute("ffhs_drag:container")) {
    target.insertBefore(toSet, target.firstChild);
  }
}
function moveField(target, dragElement) {
  let targetParent = getParent(target);
  let sourceParent = getParent(dragElement);
  let sameContainer = sourceParent === targetParent;
  let group = targetParent.getAttribute("ffhs_drag:group");
  let targetData = Alpine.mergeProxies(targetParent._x_dataStack);
  let sourceData = Alpine.mergeProxies(sourceParent._x_dataStack);
  let targetState = targetData.wire.get(targetData.statePath, "");
  let sourceState = sourceData.wire.get(sourceData.statePath, "");
  if (!targetState || Array.isArray(targetState)) targetState = {};
  if (!sourceState || Array.isArray(sourceState)) sourceState = {};
  moveElementToOnOtherElement(target, dragElement);
  if (!sameContainer) {
    let dragKey = dragElement.getAttribute("ffhs_drag:element");
    targetState[dragKey] = sourceState[dragKey];
    delete sourceState[dragKey];
    updatePositions(sourceState, sourceParent, group, sourceData);
    sourceData.wire.set(sourceData.statePath, sourceState);
  }
  updatePositions(targetState, targetParent, group, targetData);
  targetData.wire.set(targetData.statePath, targetState);
  if (!sameContainer) targetData.wire.$commit();
  else updateLiveState(targetData);
}
function handleRunAction(target, dragElement) {
  let targetParent = getParent(target);
  let group = targetParent.getAttribute("ffhs_drag:group");
  let targetData = Alpine.mergeProxies(targetParent._x_dataStack);
  let targetState = targetData.wire.get(targetData.statePath, "");
  let isFlatten = targetData.flatten;
  if (Array.isArray(targetState)) targetState = {};
  let temporaryChild = document.createElement("span");
  let keySplit = crypto.randomUUID().split("-");
  let key = keySplit[0] + keySplit[1];
  temporaryChild.setAttribute("ffhs_drag:element", key);
  temporaryChild.setAttribute("ffhs_drag:group", group);
  let cloneState = JSON.parse(JSON.stringify(targetState));
  moveElementToOnOtherElement(target, temporaryChild);
  updatePositions(cloneState, targetParent, group, targetData);
  let position;
  if (isFlatten) position = cloneState[key][targetData.dragDropPosAttribute];
  else position = cloneState[key][targetData.orderAttribute];
  let targetIn = null;
  if (isFlatten) targetIn = findTarget(temporaryChild.parentNode, ["ffhs_drag:element"]);
  if (targetIn) targetIn = targetIn.getAttribute("ffhs_drag:element");
  let targetId = target.getAttribute("ffhs_drag:element");
  let toHandle = dragElement.getAttribute("ffhs_drag:action");
  let toHandlePath = toHandle.split("'")[1];
  let toHandleAction = toHandle.split("'")[3];
  targetData.wire.mountFormComponentAction(
    toHandlePath,
    toHandleAction,
    { targetPath: targetData.statePath, position, flatten: isFlatten, targetIn, target: targetId }
  );
}
function handleDrop(target) {
  let dragElement = findDragElement();
  if (dragElement == null) return;
  if (dragElement === target) return;
  if (!hasSameGroup(dragElement, target)) return;
  if (dragElement.hasAttribute("ffhs_drag:action")) handleRunAction(target, dragElement);
  else moveField(target, dragElement);
}
function getParent(target) {
  let currentParent = target;
  while (currentParent && !(currentParent instanceof Document)) {
    if (currentParent.hasAttribute("ffhs_drag:parent")) return currentParent;
    currentParent = currentParent.parentNode;
  }
  return null;
}
function findTarget(target, attributes = ["ffhs_drag:container", "ffhs_drag:drag"]) {
  let currentParent = target;
  while (currentParent && !(currentParent instanceof Document)) {
    for (const attribute of attributes)
      if (currentParent.hasAttribute(attribute)) return currentParent;
    currentParent = currentParent.parentNode;
  }
  return null;
}
function findDragElement() {
  return document.querySelector("[ffhs_drag\\:dragging]");
}
function clearBackground() {
  document.querySelectorAll("*").forEach((element) => {
    element.classList.remove("dark:!bg-sky-950");
    element.classList.remove("!bg-blue-100");
  });
}
function hasSameGroup(elment1, elment2) {
  let dragGroup = elment1.getAttribute("ffhs_drag:group");
  let targetGroup = elment2.getAttribute("ffhs_drag:group");
  return dragGroup === targetGroup;
}
function setupDragOverEffect(element) {
  element.addEventListener("dragenter", (e) => {
    let dragElement = findDragElement();
    if (dragElement == null) return;
    if (!dragElement.hasAttribute("ffhs_drag:group")) return;
    let target = findTarget(e.target);
    if (!target) return;
    if (!hasSameGroup(dragElement, target)) return;
    e.stopPropagation();
    e.preventDefault();
    setTimeout(() => {
      target.classList.add("!bg-blue-100");
      target.classList.add("dark:!bg-sky-950");
    }, 0);
  });
  element.addEventListener("dragleave", (e) => {
    let dragElement = findDragElement();
    if (dragElement == null) return;
    if (!dragElement.hasAttribute("ffhs_drag:group")) return;
    let target = findTarget(e.target);
    if (!target) return;
    if (!hasSameGroup(dragElement, target)) return;
    e.stopPropagation();
    clearBackground();
  });
}
function setupDraggable(fieldEl) {
  fieldEl.addEventListener("dragstart", (e) => {
    e.stopPropagation();
    let target = findTarget(e.target, ["ffhs_drag:drag"]);
    target.setAttribute("ffhs_drag:dragging", true);
  });
  fieldEl.addEventListener("dragend", (e) => {
    e.stopPropagation();
    findTarget(e.target, ["ffhs_drag:drag"]).removeAttribute("ffhs_drag:dragging");
  });
  fieldEl.addEventListener("dragover", (e) => e.preventDefault());
}
function setUpDropField(element) {
  element.addEventListener("drop", (e) => {
    e.stopPropagation();
    clearBackground();
    let target = findTarget(e.target);
    handleDrop(target);
  });
}
function setupDomElement(element) {
  setupDraggable(element);
  setupDragOverEffect(element);
  setUpDropField(element);
}

// resources/js/drag_drop_scroll.js
function setupScroll(element) {
}

// resources/js/drag_drop_parent.js
function dragDropParent(statePath, stateKey, $wire, isLive, dragDropPosAttribute, dragDropEndPosAttribute, orderAttribute, flatten) {
  return {
    statePath,
    stateKey,
    wire: $wire,
    isLive,
    dragDropPosAttribute,
    dragDropEndPosAttribute,
    orderAttribute,
    flatten,
    init() {
      setupDomElement(this.$el);
      setupScroll(this.$el);
    }
  };
}
export {
  dragDropParent as default
};
