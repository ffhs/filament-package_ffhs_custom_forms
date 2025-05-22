<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\TextInput;

class EditCustomFields extends DragDropComponent
{
    protected function setUp(): void
    {
        $this->label("");

        $this->flatten();
        $this->dragDropGroup("custom_fields");

        $this->gridSize(config("ffhs_custom_forms.default_column_count"));
        $this->nestedFlattenListType(CustomField::class);

        $this->itemGridSize($this->getFieldGridSize(...));
        $this->itemGridStart($this->getFieldGridStart(...));
        $this->itemIcons($this->getFieldIcons(...));
        $this->itemLabel($this->getFieldLabel(...));
        $this->itemActions($this->getFieldActions(...));

        $this->flattenGrid($this->getFieldFlattenGrid(...));
        $this->flattenViewHidden($this->getFieldFlattenViewHidden(...));
        $this->flattenView($this->getFieldFlattenView(...));
        $this->flattenViewLabel(CustomForms::__('custom_forms.fields.name_multiple'));

        $this->schema(fn(CustomForm $record) => [
            TextInput::make('name.' . $record->getLocale()) //TODO CONTINUE
            ->label('')
                ->visible(function ($get) {
                    $type = CustomForms::getFieldTypeFromRawDate($get('.'));
                    return !is_null($type) && $type->hasEditorNameElement($get('.'));
                })
        ]);
    }

    protected function getFieldGridSize(array $itemState): int
    {
        $size = $itemState['options']['column_span'] ?? null;
        $maxSize = 12;
        if (!empty($size)) {
            return min($size, $maxSize);
        }
        $type = CustomForms::getFieldTypeFromRawDate($itemState);
        if (is_null($type) || !$type->isFullSizeField()) {
            return 1;
        }
        return $maxSize;
    }

    protected function getFieldGridStart(array $itemState): ?int
    {
        if (empty($itemState['options'])) {
            return null;
        }
        $newLineOption = $itemState['options']['new_line'] ?? false;
        return $newLineOption ? 1 : null;
    }

    protected function getFieldFlattenGrid(array $itemState): ?int
    {
        if (empty($itemState)) {
            return $this->getGridSize();
        }
        return CustomForms::getFieldTypeFromRawDate($itemState)->getColumns($itemState);
    }

    protected function getFieldFlattenViewHidden(array $itemState): bool
    {
        $type = CustomForms::getFieldTypeFromRawDate($itemState);
        return is_null($type->fieldEditorExtraComponent($itemState));
    }

    protected function getFieldFlattenView(array $itemState): ?string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState)->fieldEditorExtraComponent($itemState);
    }

    protected function getFieldIcons(array $itemState): ?string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState)->getEditorFieldIcon($itemState);
    }

    protected function getFieldLabel(array $itemState): ?string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState)->getEditorFieldTitle($itemState);
    }

    protected function getFieldActions(array $itemState, $item): array
    {
        return CustomForms::getFieldTypeFromRawDate($itemState)->getEditorActions($item, $itemState);
    }
}
