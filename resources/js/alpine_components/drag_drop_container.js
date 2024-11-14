import {setUpDropField} from "../drag_drop_dropping.js";
import {setupDragOverEffect} from "../drag_drop_hover_effect.js";

export default function dragDropContainer(group){
    return {
        group: group,
        container: true,
        action: null,
        parent: false,
        element: null,
        drag: false,

        init() {
            setupDragOverEffect(this.$el)
            setUpDropField(this.$el)
        }
    }
}
