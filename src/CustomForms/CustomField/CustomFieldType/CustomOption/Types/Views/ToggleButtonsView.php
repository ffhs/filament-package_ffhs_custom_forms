<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\ToggleButtons;

class ToggleButtonsView implements FieldTypeView
{

    use HasCustomOptionInfoListViewWithBoolean;
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        /**@var ToggleButtons $toggles */
        $toggles = static::makeComponent(ToggleButtons::class, $record, ['column_span']);

        if (FieldMapper::getOptionParameter($record, "grouped")) {
            $toggles->grouped();
        } else {
            if (!FieldMapper::getOptionParameter($record, "inline")) {
                $toggles->columnSpan(FieldMapper::getOptionParameter($record, "column_span"));
            }
        }

        if (!FieldMapper::getOptionParameter($record, "boolean")) {
            $toggles->options(FieldMapper::getAvailableCustomOptions($record));
        }

        return $toggles;
    }


}
