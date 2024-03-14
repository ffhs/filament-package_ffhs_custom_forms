<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Radio;
use Filament\Infolists\Components\IconEntry;

class RadioTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView{
        HasCustomOptionInfoListView::getInfolistComponent as getInfolistComponentParent;
    }

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        $radio = Radio::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->inline(FormMapper::getOptionParameter($record,"inline"))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record))
            ->required($record->required);

        if(FormMapper::getOptionParameter($record,"boolean")) $radio->boolean();
        else $radio->options($type->getAvailableCustomOptions($record));

        return $radio;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(!FormMapper::getOptionParameter($record, "boolean"))
            return self::getInfolistComponentParent($type,$record,$parameter);
        else
            return IconEntry::make(FormMapper::getIdentifyKey($record))
                ->label(FormMapper::getToolTips($record). ":")
                ->state(FormMapper::getAnswer($record))
                ->columnSpanFull()
                ->inlineLabel()
                ->boolean();
    }
}
