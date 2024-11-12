import {setupDomElement} from './drag_drop_script.js'

export default function dragDropElement(){
    return {
        init() {
            setupDomElement(this.$el)
        }
    }
}
