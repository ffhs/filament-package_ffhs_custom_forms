import {setupDomElement, setupDraggable} from './drag_drop_script.js'

export default function dragDropElement(onlyDrag = false){
    return {
        init() {
            if(onlyDrag) setupDraggable(this.$el)
            else setupDomElement(this.$el)
        }
    }
}
