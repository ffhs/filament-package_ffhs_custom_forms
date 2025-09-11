<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field;


use Ffhs\FfhsUtils\Filament\DragDrop\DragDropGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormGroupName;

class EditFieldsGroup extends DragDropGroup
{
    use HasFormConfiguration;
    use HasFormGroupName;

    protected function setUp(): void
    {
        parent::setUp();
        $this
            ->itemSize($this->getFieldGridSize(...))
            ->itemColumn($this->getFieldItemColumn(...))
            ->group($this->getGroupName(...))
            ->itemIcons($this->getFieldIcon(...))
            ->itemLabel($this->getFieldLabel(...))
            ->hiddenLabel()
            ->schema([
                EditField::make()
                    ->formConfiguration($this->getFormConfiguration(...))
            ]);
    }

    protected function getFieldGridSize(array $itemState): int
    {
        $size = $itemState['options']['column_span'] ?? null;
        $maxSize = 12;

        if (!empty($size)) {
            return min($size, $maxSize);
        }

        $type = CustomForms::getFieldTypeFromRawDate($itemState, $this->getFormConfiguration());

        return $type->isFullSizeField() ? $maxSize : 1;
    }

    protected function getFieldItemColumn($itemState): ?int
    {
        $newLine = $itemState['options']['new_line'] ?? null;
        return $newLine ? 1 : null;
    }

    protected function getFieldLabel($itemState): string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState, $this->getFormConfiguration())->getTranslatedName();
    }

    protected function getFieldIcon($itemState): string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState,
            $this->getFormConfiguration())->getEditorFieldIcon($itemState, $this->getFormConfiguration());
    }
}
