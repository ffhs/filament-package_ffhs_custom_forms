import {setUpDropField,} from '../drag_drop_script.js'
import {setupDragOverEffect} from "../drag_drop_hover_effect.js";
import {setupDraggable} from "../drag_drop_draging.js";

export default function dragDropElement(group, element){
    return {
        group: group,
        element: element,
        drag: true,
        parent: false,
        action: null,
        container: false,
        init() {
            setupDraggable(this.$el)
            setupDragOverEffect(this.$el)
            setUpDropField(this.$el)
        }
    }
}
