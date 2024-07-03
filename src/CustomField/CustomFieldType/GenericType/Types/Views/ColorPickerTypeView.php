<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\ColorPicker;
use Filament\Infolists\Components\ColorEntry;

class ColorPickerTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
                                            array           $parameter = []): ColorPicker {
        $picker =  ColorPicker::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->helperText(FieldMapper::getToolTips($record))
            ->label(FieldMapper::getLabelName($record))
;

        $colorType = FieldMapper::getOptionParameter($record,"color_type");

        switch ($colorType){
            case "hsl":
                $picker->hsl();
                break;
            case "rgb":
                $picker->rgb();
                break;
            case "rgba":
                $picker->rgba();
                break;
        }

        return  $picker;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): ColorEntry {
        return ColorEntry::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label(FieldMapper::getLabelName($record). ":")
            ->state(FieldMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel();
    }

}
