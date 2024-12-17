// resources/js/drag_drop/register_events.js
function registerEvent(eventName, element, event) {
  if (!element[`__has_${eventName}`]) {
    const handler = (e) => event(e);
    element.addEventListener(eventName, handler);
    element[`__has_${eventName}`] = true;
  }
}

// resources/js/drag_drop/get_values.js
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

// resources/js/drag_drop/draging.js
function setupDraggable(element) {
  registerEvent("dragstart", element, (event) => {
    event.dataTransfer.setData("text/plain", null);
    event.stopPropagation();
    let parent = getParent(element);
    if (parent !== null && parent.getAttribute("disabled")) return;
    element.setAttribute("ffhs_drag:dragging", true);
  });
  element.addEventListener("dragend", (e) => {
    e.stopPropagation();
    element.removeAttribute("ffhs_drag:dragging");
  });
}

// resources/js/drag_drop/alpine_components/action.js
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
