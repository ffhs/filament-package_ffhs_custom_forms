export default function dragDropContainer(group) {
    return {
        group: group,
        container: true,
        action: null,
        element: null,
        parent: false,
        drag: false,

        init() {
            Sortable.create(this.$el, {
                ghostClass: 'drag-drop--element__ghost_effect',
                group: group,
                animation: 150
            });

            // setupDragOverEffect(this.$el)
            // setUpDropField(this.$el)
        }
    }
}
