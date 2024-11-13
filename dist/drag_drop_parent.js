// resources/js/drag_drop_scroll.js
function setupScroll(element) {
}

// resources/js/alpine_components/drag_drop_parent.js
function dragDropParent(group, statePath, stateKey, $wire, isLive, dragDropPosAttribute, dragDropEndPosAttribute, orderAttribute, flatten) {
  return {
    group,
    statePath,
    stateKey,
    wire: $wire,
    isLive,
    dragDropPosAttribute,
    dragDropEndPosAttribute,
    orderAttribute,
    flatten,
    parent: true,
    element: null,
    drag: false,
    container: false,
    init() {
      setupScroll(this.$el);
    }
  };
}
export {
  dragDropParent as default
};
