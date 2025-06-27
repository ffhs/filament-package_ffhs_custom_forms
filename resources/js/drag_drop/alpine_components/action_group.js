import {getAlpineData, getParent} from '../get_values.js';
import {updatePositions} from '../move_elements.js';

export default function dragDropActionGroup(group) {
    return {
        group: group,
        parent: false,
        element: null,
        container: false,
        init() {
            Sortable.create(this.$el, {
                filter: '.disabled-drag_drop',
                ghostClass: 'drag-drop--action__ghost_effect',
                dragClass: 'drag-drop--action__drag',
                animation: 150,
                forceFallback: true,
                swapThreshold: 0.65,
                sort: false,
                group: {
                    name: group,
                    pull: 'clone',
                    put: false
                },
                onEnd: getOnEndCallback(group),
            });
        }
    }
}

function getOnEndCallback(group) {
    return function (evt) {
        if (evt.pullMode !== 'clone') {
            return;
        }

        const clonedElement = evt.item;
        let action = clonedElement.getAttribute('ffhs_drag:action')
        let targetParent = getParent(clonedElement)
        let targetParentData = getAlpineData(targetParent)
        let $wire = targetParentData.wire
        let targetState = $wire.get(targetParentData.statePath, '')

        if (Array.isArray(targetState)) {
            targetState = {}
        }

        let temporaryKey = generateElementKey();
        let temporaryChild = document.createElement('div');

        clonedElement.replaceWith(temporaryChild)
        temporaryChild.setAttribute('x-data', `typeof dragDropElement === 'undefined'? {}: dragDropElement('${group}','${temporaryKey}')`)
        temporaryChild.setAttribute('ffhs_drag:component', null)
        temporaryChild.classList.add('hidden')
        Alpine.initTree(temporaryChild);

        // Clone State to find position without updating the real state
        let cloneState = JSON.parse(JSON.stringify(targetState))
        updatePositions(cloneState, targetParent, group, targetParentData)

        let metaData = {
            targetPath: targetParentData.statePath,
            flatten: targetParentData.flatten,
            stateWithTempField: cloneState,
            temporaryKey: temporaryKey,
            state: JSON.parse(JSON.stringify(targetState))
        };

        if (targetParent.getAttribute('disabled')) {
            return;
        }

        let toActionPath = action.split("'")[1]
        let toDoAction = action.split("'")[3]

        $wire.mountFormComponentAction(toActionPath, toDoAction, metaData);
    }
}

function generateElementKey() {
    let keySplit = crypto.randomUUID().split('-');

    return keySplit[0] + keySplit[1];
}
