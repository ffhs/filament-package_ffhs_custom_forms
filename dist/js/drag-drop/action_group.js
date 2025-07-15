// resources/js/drag_drop/get_values.js
function getAlpineData(element) {
  if (element._x_dataStack === void 0) {
    return {};
  }
  return Alpine.mergeProxies(element._x_dataStack);
}
function getGroup(element) {
  return getAlpineData(element).group ?? null;
}
function getElementKey(element) {
  let alpine = getAlpineData(element);
  if (alpine === null) {
    return null;
  }
  return alpine.element ?? null;
}
function isParent(element) {
  return getAlpineData(element).parent ?? false;
}
function getParent(element) {
  let currentParent = element;
  while (currentParent && !(currentParent instanceof Document)) {
    if (currentParent.hasAttribute("ffhs_drag:component")) {
      if (isParent(currentParent)) {
        return currentParent;
      }
    }
    currentParent = currentParent.parentNode;
  }
  return null;
}

// resources/js/drag_drop/move_elements.js
function flattenElementCheck(element, data) {
  let elementKey = getElementKey(element);
  if (elementKey === null) {
    return false;
  }
  let parentElement = getParent(element);
  let parentData = getAlpineData(parentElement);
  return parentData.statePath === data.statePath;
}
function countFlattenChildren(container, data) {
  let count = 0;
  container.querySelectorAll("[ffhs_drag\\:component]").forEach((element) => {
    if (!flattenElementCheck(element, data)) {
      return;
    }
    count++;
  });
  return count;
}
function updatePositionsFlatten(state, container, group, data) {
  let currentPos = 1;
  let dragDropPosAttribute = data.dragDropPosAttribute;
  let dragDropEndPosAttribute = data.dragDropEndPosAttribute;
  let usedKeys = [];
  container.querySelectorAll("[ffhs_drag\\:component]").forEach((element) => {
    if (!flattenElementCheck(element, data)) {
      return;
    }
    let elementKey = getElementKey(element);
    let contains = countFlattenChildren(element, data);
    if (state[elementKey] === void 0) {
      state[elementKey] = {};
    }
    usedKeys.push(elementKey);
    state[elementKey][dragDropPosAttribute] = currentPos;
    state[elementKey][dragDropEndPosAttribute] = contains === 0 ? null : currentPos + contains;
    currentPos++;
  });
  let notUsedKeys = Object.keys(state).filter((x) => !usedKeys.includes(x));
  notUsedKeys.forEach((x) => delete state[x]);
}
function updatePositionsOrder(state, container, group, data) {
  let currentPos = 1;
  let orderAttribute = data.orderAttribute;
  let parentContainer = getParent(container);
  container.querySelectorAll("[ffhs_drag\\:component]").forEach((element) => {
    let elementKey = getElementKey(element);
    if (!elementKey || getGroup(element) !== group) {
      return;
    }
    let parentElement = getParent(element);
    if (parentContainer !== parentElement) {
      return;
    }
    if (state[elementKey] === void 0) {
      state[elementKey] = {};
    }
    state[elementKey][orderAttribute] = currentPos;
    currentPos++;
  });
}
function updatePositions(state, container, group, parentData) {
  if (parentData.flatten) {
    updatePositionsFlatten(state, container, group, parentData);
  } else if (parentData.orderAttribute !== null) {
    updatePositionsOrder(state, container, group, parentData);
  }
}

// resources/js/drag_drop/alpine_components/action_group.js
function dragDropActionGroup(group) {
  return {
    group,
    parent: false,
    element: null,
    container: false,
    init() {
      Sortable.create(this.$el, {
        filter: ".disabled-drag_drop",
        ghostClass: "drag-drop--action__ghost_effect",
        dragClass: "drag-drop--action__drag",
        animation: 150,
        forceFallback: true,
        swapThreshold: 0.65,
        sort: false,
        group: {
          name: group,
          pull: "clone",
          put: false
        },
        onEnd: getOnEndCallback(group)
      });
    }
  };
}
function getOnEndCallback(group) {
  return function(evt) {
    if (evt.pullMode !== "clone") {
      return;
    }
    const clonedElement = evt.item;
    let action = clonedElement.getAttribute("ffhs_drag:action");
    let targetParent = getParent(clonedElement);
    let targetParentData = getAlpineData(targetParent);
    let $wire = targetParentData.wire;
    let targetState = $wire.get(targetParentData.statePath, "");
    if (Array.isArray(targetState)) {
      targetState = {};
    }
    let temporaryKey = generateElementKey();
    let temporaryChild = document.createElement("div");
    clonedElement.replaceWith(temporaryChild);
    temporaryChild.setAttribute("x-data", `
                {
                    group: '${group}',
                    element: '${temporaryKey}',
                    parent: false,
                    container: false,
                }`);
    temporaryChild.setAttribute("ffhs_drag:component", null);
    temporaryChild.classList.add("hidden");
    Alpine.initTree(temporaryChild);
    let cloneState = JSON.parse(JSON.stringify(targetState));
    updatePositions(cloneState, targetParent, group, targetParentData);
    let metaData = {
      targetPath: targetParentData.statePath,
      flatten: targetParentData.flatten,
      stateWithTempField: cloneState,
      temporaryKey,
      state: JSON.parse(JSON.stringify(targetState))
    };
    if (targetParent.getAttribute("disabled")) {
      return;
    }
    let toActionPath = action.split("'")[1];
    let toDoAction = action.split("'")[3];
    $wire.mountFormComponentAction(toActionPath, toDoAction, metaData);
  };
}
function generateElementKey() {
  let keySplit = crypto.randomUUID().split("-");
  return keySplit[0] + keySplit[1];
}
export {
  dragDropActionGroup as default
};
