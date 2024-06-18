<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Radio;

class RadioTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListViewWithBoolean;

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        $radio = Radio::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->inline(FieldMapper::getOptionParameter($record,"inline"))
            ->helperText(FieldMapper::getToolTips($record))
            ->label(FieldMapper::getLabelName($record))
;

        if(FieldMapper::getOptionParameter($record,"boolean")) $radio->boolean();
        else $radio->options(FieldMapper::getAvailableCustomOptions($record));

        return $radio;
    }


}
