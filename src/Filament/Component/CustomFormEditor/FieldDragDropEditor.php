<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

class FieldDragDropEditor extends DragDropComponent
{
    protected function setUp(): void
    {
        $this->label('');

        $this->flatten();
        $this->dragDropGroup(fn(Get $get) => 'custom_fields-' . $get('custom_form_identifier'));

        $this->gridSize(CustomForms::config('default_column_count'));
        $this->nestedFlattenListType(CustomField::class);

        $this->itemGridSize($this->getFieldGridSize(...));
        $this->itemGridStart($this->getFieldGridStart(...));
        $this->itemIcons($this->getFieldIcons(...));
        $this->itemLabel($this->getFieldLabel(...));
        $this->itemActions($this->getFieldActions(...));

        $this->flattenGrid($this->getFieldFlattenGrid(...));
        $this->flattenViewHidden($this->getFieldFlattenViewHidden(...));
        $this->flattenView($this->getFieldFlattenView(...));
        $this->flattenViewLabel(CustomField::__('label.multiple'));

        $this->schema($this->getFieldSchema(...));
    }

    protected function getFieldSchema(CustomForm $record): array
    {
        return [
            TextInput::make('name.' . $record->getLocale())
                ->label('')
                ->visible(function ($get, $record) {
                    $type = CustomForms::getFieldTypeFromRawDate($get('.'), $record);
                    return !is_null($type) && $type->hasEditorNameElement($get('.'));
                })
        ];
    }

    protected function getFieldGridSize(array $itemState, CustomForm $record): int
    {
        $size = $itemState['options']['column_span'] ?? null;
        $maxSize = 12;
        if (!empty($size)) {
            return min($size, $maxSize);
        }
        $type = CustomForms::getFieldTypeFromRawDate($itemState, $record);
        return $type->isFullSizeField() ? $maxSize : 1;
    }

    protected function getFieldGridStart(array $itemState): ?int
    {
        $newLineOption = $itemState['options']['new_line'] ?? false;
        return $newLineOption ? 1 : null;
    }

    protected function getFieldFlattenGrid(array $itemState, CustomForm $record): ?int
    {
        if (empty($itemState)) {
            return $this->getGridSize();
        }
        return CustomForms::getFieldTypeFromRawDate($itemState, $record)
            ->getColumns($itemState);
    }

    protected function getFieldFlattenViewHidden(array $itemState, CustomForm $record): bool
    {
        $type = CustomForms::getFieldTypeFromRawDate($itemState, $record);
        return is_null($type->fieldEditorExtraComponent($itemState));
    }

    protected function getFieldFlattenView(array $itemState, CustomForm $record): ?string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState, $record)
            ->fieldEditorExtraComponent($itemState);
    }

    protected function getFieldIcons(array $itemState, $record): ?string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState, $record)
            ->getEditorFieldIcon($itemState, $record);
    }

    protected function getFieldLabel(array $itemState, $record): ?string
    {
        $title = CustomForms::getFieldTypeFromRawDate($itemState, $record)->getEditorFieldTitle($itemState, $record);
        $badge = CustomForms::getFieldTypeFromRawDate($itemState, $record)->getEditorFieldBadge($itemState, $record);

        if (is_null($badge)) {
            return $title;
        }

        return '<div class="flex">' . $badge . '<p>' . $title . '</p> </div>';
    }

    protected function getFieldActions(array $itemState, $item, $record): array
    {
        return CustomForms::getFieldTypeFromRawDate($itemState, $record)->getEditorActions($item, $itemState);
    }
}
