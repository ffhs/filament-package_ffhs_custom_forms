<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field;


use Ffhs\FfhsUtils\Filament\DragDrop\DragDropGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormGroupName;
use Illuminate\Support\HtmlString;

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

    protected function getFieldGridSize(array $itemState, $get): int|string
    {
        $type = CustomForms::getFieldTypeFromRawDate($itemState, $this->getFormConfiguration());
        if ($type->isFullSizeField()) {
            return 'full';
        }

        $size = $itemState['options']['column_span'] ?? null;
        $maxSize = $get('../options/column') ?? $this->getFormConfiguration()->getColumns() ?? 12;

        return empty($size) ? 1 : min($size, $maxSize);
    }

    protected function getFieldItemColumn($itemState): ?int
    {
        $newLine = $itemState['options']['new_line'] ?? null;
        return $newLine ? 1 : null;
    }

    protected function getFieldLabel($itemState): string
    {
        $type = CustomForms::getFieldTypeFromRawDate($itemState, $this->getFormConfiguration());
        $name = $type->getEditorFieldTitle($itemState, $this->getFormConfiguration());
        $name = htmlspecialchars($name);
        $badge = $type->getEditorFieldBadge($itemState, $this->getFormConfiguration());

        $label = $badge . $name;
        return new HtmlString($label);
    }

    protected function getFieldIcon($itemState): string
    {
        return CustomForms::getFieldTypeFromRawDate($itemState,
            $this->getFormConfiguration())->getEditorFieldIcon($itemState, $this->getFormConfiguration());
    }
}
