export default function dragDropParent(
    group,
    statePath,
    stateKey,
    $wire,
    isLive,
    dragDropPosAttribute,
    dragDropEndPosAttribute,
    orderAttribute,
    flatten
) {
    return {
        group: group,
        statePath: statePath,
        stateKey: stateKey,
        wire: $wire,
        isLive: isLive,

        dragDropPosAttribute: dragDropPosAttribute,
        dragDropEndPosAttribute: dragDropEndPosAttribute,

        orderAttribute: orderAttribute,
        flatten: flatten,


        parent: true,
        element: null,
        drag: false,
        container: false,

        init() {

        }
    }
}
