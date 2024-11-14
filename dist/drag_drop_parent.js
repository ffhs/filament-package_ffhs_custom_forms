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
    }
  };
}
export {
  dragDropParent as default
};
