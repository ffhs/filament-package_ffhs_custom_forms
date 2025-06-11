<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\ToggleButtons;

class ToggleButtonsView implements FieldTypeView
{
    use HasCustomOptionInfoListViewWithBoolean;
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        /**@var ToggleButtons $toggles */
        $toggles = $this->makeComponent(ToggleButtons::class, $record, ['column_span']);

        if ($this->getOptionParameter($record, 'grouped')) {
            $toggles->grouped();
        } elseif (!$this->getOptionParameter($record, 'inline')) {
            $toggles->columnSpan($this->getOptionParameter($record, 'column_span'));
        }

        if (!$this->getOptionParameter($record, 'boolean')) {
            $toggles->options($this->getAvailableCustomOptions($record));
        }

        return $toggles;
    }
}
