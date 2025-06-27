// resources/js/drag_drop/alpine_components/element.js
function dragDropElement(group, element) {
  return {
    group,
    element,
    parent: false,
    container: false,
    init() {
    }
  };
}
export {
  dragDropElement as default
};
