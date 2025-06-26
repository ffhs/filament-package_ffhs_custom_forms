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
    }
  };
}
export {
  dragDropAction as default
};
