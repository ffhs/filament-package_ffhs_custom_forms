<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class SelectTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {
        $select = Select::make($type::getIdentifyKey($record))
            ->options(fn()=> $record->customOptions->pluck("name_de","identifier"))//ToDo Translate
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record))
            ->required($record->required);

        if($type->getOptionParameter($record,"several")){
            $select->multiple()
                ->minItems($record->required?$type->getOptionParameter($record,"min_select"):0)
                ->maxItems($type->getOptionParameter($record,"max_select"));
        }


        return $select;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {
        return TextEntry::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->label($type::class::getLabelName($record). ":")
            ->columnSpanFull()
            ->inlineLabel()
            ->badge()
            ->state(function () use ($type, $record){
                if(empty($type->answare($record)) || empty($type->answare($record))) return "";
                return $record
                    ->customFieldVariation
                    ->customOptions
                    ->pluck("name_de","identifier")
                    ->filter(fn($value, $id) => in_array($id,$type->answare($record)));
                  //ToDo Translate
            });
    }

}
