<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Radio;
use Filament\Infolists\Components\IconEntry;

class RadioTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView{
        HasCustomOptionInfoListView::getInfolistComponent as getInfolistComponentParent;
    }

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {

        $radio = Radio::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->inline($type->getOptionParameter($record,"inline"))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record))
            ->required($record->required);

        if($type->getOptionParameter($record,"boolean")) $radio->boolean();
        else $radio->options($type->getAvailableCustomOptions($record));

        return $radio;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(!$type->getOptionParameter($record, "boolean"))
            return self::getInfolistComponentParent($type,$record,$parameter);
        else
            return IconEntry::make($type::getIdentifyKey($record))
                ->label($type::getLabelName($record). ":")
                ->state($type->answare($record))
                ->columnSpanFull()
                ->inlineLabel()
                ->boolean();
    }
}
