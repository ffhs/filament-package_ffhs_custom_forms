import {setupDomElement} from './drag_drop_script.js'
import {setupScroll} from './drag_drop_scroll.js'

export default function dragDropParent(
    statePath,
    stateKey,
    $wire,
    isLive,
    dragDropPosAttribute,
    dragDropEndPosAttribute,
    orderAttribute,
    flatten
){
    return {

        statePath: statePath,
        stateKey: stateKey,
        wire: $wire,
        isLive: isLive,

        dragDropPosAttribute: dragDropPosAttribute,
        dragDropEndPosAttribute: dragDropEndPosAttribute,

        orderAttribute: orderAttribute,
        flatten: flatten,

        init() {
            setupDomElement(this.$el)
            setupScroll(this.$el)
        }
    }
}
