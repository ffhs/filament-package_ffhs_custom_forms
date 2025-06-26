// resources/js/drag_drop/draging.js
function setupDraggable(element) {
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
