import {setupDraggable} from "../drag_drop_draging.js";

export default function dragDropAction(group, action){
    return {
        group: group,
        action: action,
        drag: true,
        parent: false,
        element: null,
        container: false,
        init() {
            setupDraggable(this.$el)
        }
    }
}
