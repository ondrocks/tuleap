import { element } from "angular";
import { isNull } from "lodash";

export default KanbanColumnController;

KanbanColumnController.$inject = [
    "$rootScope",
    "$scope",
    "$element",
    "DroppedService",
    "KanbanColumnService",
    "SharedPropertiesService",
    "ColumnCollectionService"
];

function KanbanColumnController(
    $rootScope,
    $scope,
    $element,
    DroppedService,
    KanbanColumnService,
    SharedPropertiesService,
    ColumnCollectionService
) {
    var self = this;
    self.appending_item = false;
    self.cancelDrag = cancelDrag;
    self.dragularOptions = dragularOptions;
    self.isColumnLoadedAndEmpty = isColumnLoadedAndEmpty;

    function dragularOptions() {
        return {
            containersModel: self.column.filtered_content,
            scope: $scope,
            revertOnSpill: true,
            moves: isItemDraggable,
            onInit: initDragular
        };
    }

    function initDragular(drake) {
        self.drake = drake;

        $element.on("dragularenter", dragularEnter);
        $element.on("dragularleave", dragularLeave);
        $element.on("dragularrelease", dragularRelease);
        $scope.$on("dragulardrag", dragularDrag);
        $scope.$on("dragulardrop", dragularDrop);
    }

    function isItemDraggable(element_to_drag, container, handle_element) {
        return !ancestorCannotBeDragged(handle_element);
    }

    function ancestorCannotBeDragged(handle_element) {
        return element(handle_element).closest('[data-nodrag="true"]').length > 0;
    }

    function dragularEnter() {
        self.appending_item = true;
        $scope.$apply();
    }

    function dragularLeave() {
        self.appending_item = false;
        $scope.$apply();
    }

    function dragularRelease() {
        self.appending_item = false;
        $scope.$apply();
    }

    function dragularDrag(event) {
        event.stopPropagation();

        ColumnCollectionService.cancelWipEditionOnAllColumns();

        $scope.$apply();
    }

    function dragularDrop(
        event,
        dropped_item_element,
        target_element,
        source_element,
        source_model,
        initial_index,
        target_model,
        target_index
    ) {
        event.stopPropagation();

        var target_column_id = getColumnId(element(target_element));
        var source_column = self.column;
        var target_column = ColumnCollectionService.getColumn(target_column_id);
        var target_model_items = !isNull(target_model) ? target_model : source_model;
        var current_kanban = SharedPropertiesService.getKanban();
        var dropped_item = target_model_items[target_index];
        var compared_to = DroppedService.getComparedTo(target_model_items, target_index);

        dropped_item.updating = true;

        var promise;
        if (droppedToTheSameColumn(source_column, target_column)) {
            promise = DroppedService.reorderColumn(
                current_kanban.id,
                target_column_id,
                dropped_item.id,
                compared_to
            );
        } else {
            promise = DroppedService.moveToColumn(
                current_kanban.id,
                target_column_id,
                dropped_item.id,
                compared_to,
                dropped_item.in_column
            );
        }

        promise.then(function() {
            dropped_item.updating = false;
        });

        KanbanColumnService.moveItem(dropped_item, source_column, target_column, compared_to);

        reflowKustomScrollBars();
    }

    function droppedToTheSameColumn(source_column, target_column) {
        return source_column.id === target_column.id;
    }

    function getColumnId(html_element) {
        return html_element.data("column-id");
    }

    function isColumnLoadedAndEmpty() {
        return !self.column.loading_items && self.column.content.length === 0;
    }

    function reflowKustomScrollBars() {
        $rootScope.$broadcast("rebuild:kustom-scroll");
    }

    function cancelDrag() {
        self.appending_item = false;
        self.drake.cancel();
    }
}
