<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

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

    public static function getPrioritizedSelect(CustomFieldType $type, CustomField $record, array $parameter): Group
    {
        $helpText = FieldMapper::getOptionParameter($record, 'helper_text') ?? null;
        $label = FieldMapper::getLabelName($record);

        $required = FieldMapper::getOptionParameter($record,'required') ?? false;
        $options = FieldMapper::getAvailableCustomOptions($record);

        $maxSelect = FieldMapper::getOptionParameter($record,'max_select');
        $minSelect = FieldMapper::getOptionParameter($record,'min_select');
        $dynamicPrioritized = FieldMapper::getOptionParameter($record,'dynamic_prioritized');
        $preKey =  'prioritized_';


        $titleComponent = Placeholder::make($label)
            ->label($label)
            ->hidden(empty($label))
            ->dehydrated();

        $helpTextComponent = Placeholder::make($label. "help_text")
            ->helperText($helpText?new HtmlString($helpText):null)
            ->label("")
            ->dehydrated();

        $group = Group::make()
            ->columnStart(FieldMapper::getOptionParameter($record,'new_line_option'))
            ->columnSpan(FieldMapper::getOptionParameter($record,'column_span'))
            ->statePath(FieldMapper::getIdentifyKey($record));

        $selects = [];
        for ($selectId = 0; $selectId < $maxSelect; $selectId++) {
            $isSelectRequired = $required && $minSelect!= 0 && $minSelect > $selectId ;
            $select = Select::make($preKey . $selectId)
                ->required($isSelectRequired)
                ->options($options)
                ->label($selectId+1 . ". " . __('filament-package_ffhs_custom_forms::custom_forms.fields.type_view.select.select'))
                ->disableOptionWhen(function ($get, $value) use ($maxSelect, $preKey, $selectId) : bool{
                    for ($i = 0; $i < $maxSelect; $i++) {
                        if($i == $selectId) continue;
                        if($get($preKey . $i) == $value) return true;
                    }
                    return false;
                });

                if($minSelect-1 <= $selectId && $dynamicPrioritized) {
                    $select->afterStateUpdated(function ($set, $state) use ($minSelect, $preKey, $maxSelect, $selectId) {
                        if($state != null) return;
                        for ($i = $selectId; $i < $maxSelect; $i++) {
                            if($minSelect <= $i) $set($preKey . $i, null);
                        }
                    });
                }


            if($dynamicPrioritized && $minSelect <= $selectId && $selectId > 0){
                $select->whenTruthy($preKey . $selectId-1);
            }

            $selects[] = $select;
        }

        return $group->schema(array_merge(
            [$titleComponent],
            $selects,
            [$helpTextComponent],
        ));




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
