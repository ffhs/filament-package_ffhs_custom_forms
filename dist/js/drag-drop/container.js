// resources/js/drag_drop/alpine_components/container.js
function dragDropContainer(group) {
  return {
    group,
    container: true,
    action: null,
    element: null,
    parent: false,
    drag: false,
    init() {
      Sortable.create(this.$el, {
        ghostClass: "drag-drop--element__ghost_effect",
        group,
        animation: 150
      });
    }
  };
}
export {
  dragDropContainer as default
};
