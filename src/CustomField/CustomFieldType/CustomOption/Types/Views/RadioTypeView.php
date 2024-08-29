<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\ToggleButtons;

class RadioTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListViewWithBoolean;
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array  $parameter = []): Component {
        $radio = static::makeComponent(Radio::class, $record)
            ->columns(FieldMapper::getOptionParameter($record,"columns"))
            ->inline(FieldMapper::getOptionParameter($record,"inline"));

        if(FieldMapper::getOptionParameter($record,"boolean")) $radio->boolean();
        else $radio->options(FieldMapper::getAvailableCustomOptions($record));

        return $radio;
    }


}
