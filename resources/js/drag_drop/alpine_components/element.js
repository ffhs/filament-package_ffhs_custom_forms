import {setUpDropField,} from '../dropping.js'
import {setupDragOverEffect} from "../hover_effect.js";
import {setupDraggable} from "../draging.js";

export default function dragDropElement(group, element){
    return {
        group: group,
        element: element,
        drag: true,
        parent: false,
        action: null,
        container: false,
        init() {
            console.log('Start up dragDropElement');
            setupDraggable(this.$el)
            setupDragOverEffect(this.$el)
            setUpDropField(this.$el)
        },
        destroy() {
            console.log('Cleaning up dragDropElement');
        }
    }
}
