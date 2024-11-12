import * as dragDropScroll from './drag_drop_scroll.js'

import {setupDomElement} from './drag_drop_script.js'

export default function dragDropContainer(
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
            dragDropScroll.setupScroll(this.$el)
        }
    }
}
