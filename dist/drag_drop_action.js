// resources/js/drag_drop_events.js
function registerEvent(eventName, element, event) {
  if (!element[`__has_${eventName}`]) {
    const handler = (e) => event(e);
    element.addEventListener(eventName, handler);
    element[`__has_${eventName}`] = true;
  }
}

// resources/js/drag_drop_values.js
function getAlpineData(element) {
  if (element._x_dataStack === void 0) return {};
  return Alpine.mergeProxies(element._x_dataStack);
}
function isParent(element) {
  return getAlpineData(element).parent ?? false;
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

// resources/js/drag_drop_draging.js
function setupDraggable(element) {
  registerEvent("dragstart", element, (event) => {
    event.stopPropagation();
    if (getParent(element).getAttribute("disabled")) return;
    element.setAttribute("ffhs_drag:dragging", true);
  });
  element.addEventListener("dragend", (e) => {
    e.stopPropagation();
    element.removeAttribute("ffhs_drag:dragging");
  });
  element.addEventListener("dragover", (e) => e.preventDefault());
}

// resources/js/alpine_components/drag_drop_action.js
function dragDropAction(group, action) {
  return {
    group,
    action,
    drag: true,
    parent: false,
    element: null,
    container: false,
    init() {
      setupDraggable(this.$el);
    }
  };
}
export {
  dragDropAction as default
};
