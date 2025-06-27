import {getAlpineData, getParent} from '../get_values.js';
import {updatePositions} from '../move_elements.js';

export default function dragDropContainer(group) {
    return {
        group: group,
        container: true,
        element: null,
        parent: false,
        init() {
            Sortable.create(this.$el, {
                ghostClass: 'drag-drop--element__ghost_effect',
                dragClass: 'drag-drop--element__drag',
                group: group,
                animation: 150,
                forceFallback: true,
                handle: '.drag_drop_handle',
                onEnd: onEndClosure(group)
            });
        }
    }
}

export function onEndClosure(group) {
    return function (evt) {
        let formParent = getParent(evt.from)
        let toParent = getParent(evt.to)
        let sameContainer = toParent === formParent;
        let toParentData = getAlpineData(toParent)
        let stateTo = toParentData.wire.get(toParentData.statePath, '')

        if (!stateTo || Array.isArray(stateTo)) {
            stateTo = {}
        }

        if (toParent.getAttribute('disabled') || formParent.getAttribute('disabled')) {
            return;
        }

        if (!sameContainer) {
            let formParentData = getAlpineData(formParent)
            let stateFrom = formParentData.wire.get(formParentData.statePath, '')

            if (!stateFrom || Array.isArray(stateFrom)) {
                stateFrom = {}
            }

            updatePositions(stateFrom, formParent, group, formParentData)
            formParentData.wire.set(formParentData.statePath, stateFrom, false)
        }

        updatePositions(stateTo, toParent, group, toParentData)
        let isLive = (!sameContainer || toParentData.isLive);
        toParentData.wire.set(toParentData.statePath, stateTo, isLive)
    }
}
