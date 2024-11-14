// resources/js/drag_drop_events.js
function registerEvent(eventName, element, event) {
  const newHandler = (e) => event(e);
  element.removeEventListener(eventName, newHandler);
  element.addEventListener(eventName, newHandler);
}

// resources/js/drag_drop_draging.js
function setupDraggable(element) {
  registerEvent("dragstart", element, (event) => {
    event.stopPropagation();
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
