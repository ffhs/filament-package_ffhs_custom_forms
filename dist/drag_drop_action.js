// resources/js/drag_drop_draging.js
function setupDraggable(elementField) {
  elementField.addEventListener("dragstart", (e) => {
    e.stopPropagation();
    elementField.setAttribute("ffhs_drag:dragging", true);
  });
  elementField.addEventListener("dragend", (e) => {
    e.stopPropagation();
    elementField.removeAttribute("ffhs_drag:dragging");
  });
  elementField.addEventListener("dragover", (e) => e.preventDefault());
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
