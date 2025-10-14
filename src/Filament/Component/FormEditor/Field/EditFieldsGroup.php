<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field;


use Ffhs\FfhsUtils\Filament\Components\DragDrop\DragDropGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormGroupName;
use Illuminate\Support\HtmlString;

class EditFieldsGroup extends DragDropGroup
{
    use HasFormConfiguration;
    use HasFormGroupName;

    public function hydrateState(?array &$hydratedDefaultState, bool $shouldCallHydrationHooks = true): void
    {
        if ($hydratedDefaultState === null) {
            $rawState = $this->getPassiveRawState();
            $rawState = new CustomFieldStateCast()->unflattenCustomFields($rawState ?? []);
            $this->rawState($rawState);
        }
        parent::hydrateState($hydratedDefaultState, $shouldCallHydrationHooks);
    }

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

    private function getPassiveRawState()
    {
        $statePath = $this->getStatePath();

        if (blank($statePath)) {
            return [];
        }

        $state = data_get($this->getLivewire(), $statePath);

        if ((!is_array($state)) && blank($state)) {
            $state = null;
        }

        return $state;
    }


}
