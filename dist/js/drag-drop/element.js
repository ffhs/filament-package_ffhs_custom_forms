// resources/js/drag_drop/alpine_components/element.js
function dragDropElement(group, element) {
  return {
    group,
    element,
    drag: true,
    parent: false,
    action: null,
    container: false,
    init() {
    }
  };
}
export {
  dragDropElement as default
};
