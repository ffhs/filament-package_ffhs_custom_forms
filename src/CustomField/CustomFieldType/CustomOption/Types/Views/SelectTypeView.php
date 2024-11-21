<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\PriorizedSelect;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;

class SelectTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView{
        getInfolistComponent as getInfolistComponentNormalSelect;
    }
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Component {

        $several = FieldMapper::getOptionParameter($record,'several');
        $prioritized = FieldMapper::getOptionParameter($record,'prioritized');

        if($several && $prioritized)
            return self::getPrioritizedSelect($type, $record, $parameter);
        else
            return self::getSingleSelect($record);
    }

    public static function getPrioritizedSelect(CustomFieldType $type, CustomField $record, array $parameter): Component|\Filament\Infolists\Components\Component
    {
        /**@var PriorizedSelect $select*/
        $select = static::makeComponent(PriorizedSelect::class, $record);

        $selectLabelTranlsation = __('filament-package_ffhs_custom_forms::custom_forms.fields.type_view.select.select');

        return $select
            ->minItems(FieldMapper::getOptionParameter($record,'min_select'))
            ->maxItems(FieldMapper::getOptionParameter($record,'max_select'))
            ->options(FieldMapper::getAvailableCustomOptions($record))
            ->dynamic(FieldMapper::getOptionParameter($record,'dynamic_prioritized'))
            ->mutateSelectUsing(function (Select $select, $selectId) use ($selectLabelTranlsation) {
                return $select
                    ->label($selectId+1 . ". " . $selectLabelTranlsation);
            });
    }


    public static function getSingleSelect(CustomField $record): Select
    {
        $select = static::makeComponent(Select::class, $record)
            ->options(FieldMapper::getAvailableCustomOptions($record));

        if (FieldMapper::getOptionParameter($record, 'several')) {
            $maxItems = FieldMapper::getOptionParameter($record, 'max_select');
            $select->multiple()->minItems(
                $record->required ? FieldMapper::getOptionParameter($record, 'min_select') : 0
            );
            if ($maxItems > 0) {
                $select->maxItems($maxItems);
            }
        }
        return $select;
    }



    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component
    {
        $several = FieldMapper::getOptionParameter($record,'several');
        $prioritized = FieldMapper::getOptionParameter($record,'prioritized');
        if(!($several && $prioritized)){
            return static::getInfolistComponentNormalSelect($type, $record, $parameter);
        }


        $textEntry = TextEntry::make(FieldMapper::getIdentifyKey($record));
        $answer = FieldMapper::getAnswer($record);
        $stateList = FieldMapper::getAllCustomOptions($record)
            ->filter(fn($value, $id) => in_array($id,$answer));

        $cleanedAnswers = [];
        if(!is_array($answer)) $answer = [];
        foreach ($answer as $key => $value) {
            if(!str_contains($key, "prioritized_")) continue;
            if($value == null) continue;
            $selectId = str_replace("prioritized_","",$key);

            $name = $stateList->toArray()[$value] ?? "";
            $translatedSelect = __('filament-package_ffhs_custom_forms::custom_forms.fields.type_view.select.select');
            $cleanedAnswers[$selectId] = $selectId+1 . ". ". $translatedSelect .": " . $name; //ToDo Translate
        }

        ksort($cleanedAnswers, SORT_NUMERIC);


        return $textEntry
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label(FieldMapper::getLabelName($record))
            ->columnSpanFull()
            ->inlineLabel()
            ->listWithLineBreaks()
            ->badge()
            ->state($cleanedAnswers);
    }

}
