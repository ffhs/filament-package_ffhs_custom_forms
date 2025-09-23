<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Components\Component;

class ToggleButtonsView implements FieldTypeView
{
    use HasCustomOptionInfoListViewWithBoolean;
    use HasDefaultViewComponent;


    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $toggles = $this->makeComponent(ToggleButtons::class, $customField, false, ['column_span']);

        if ($this->getOptionParameter($customField, 'grouped')) {
            $toggles->grouped();
        } elseif (!$this->getOptionParameter($customField, 'inline')) {
            $toggles->columnSpan($this->getOptionParameter($customField, 'column_span'));
        }

        if (!$this->getOptionParameter($customField, 'boolean')) {
            $toggles->options($this->getAvailableCustomOptions($customField));
        }

        return $toggles;
    }
}
