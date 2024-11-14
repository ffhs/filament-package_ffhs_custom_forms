import {setUpDropField} from "../dropping.js";
import {setupDragOverEffect} from "../hover_effect.js";

export default function dragDropContainer(group){
    return {
        group: group,
        container: true,
        action: null,
        element: null,
        parent: false,
        drag: false,

        init() {
            setupDragOverEffect(this.$el)
            setUpDropField(this.$el)
        }
    }
}
