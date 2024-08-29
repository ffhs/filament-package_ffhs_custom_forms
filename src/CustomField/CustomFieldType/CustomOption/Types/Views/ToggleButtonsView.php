<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;

class ToggleButtonsView implements FieldTypeView
{

    use HasCustomOptionInfoListViewWithBoolean;
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Component {
        $toggles = static::makeComponent(ToggleButtons::class, $record)
            ->columns(FieldMapper::getOptionParameter($record,"columns"));

        if(FieldMapper::getOptionParameter($record,"grouped")) $toggles->grouped();
        else if(FieldMapper::getOptionParameter($record,"inline")) $toggles->inline();
        else $toggles->columnSpan(FieldMapper::getOptionParameter($record,"column_span"));

        if(FieldMapper::getOptionParameter($record,"boolean")) $toggles->boolean();
        else $toggles->options(FieldMapper::getAvailableCustomOptions($record));

        return $toggles;
    }


}
