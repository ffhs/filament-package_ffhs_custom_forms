<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra\CustomFieldEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra\CustomFieldRuleEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra\CustomFormEditSave;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CustomFormEditForm
{
    public static function formSchema(): array {
        return [
            Fieldset::make()
                ->columnStart(1)
                ->columnSpan(1)
                ->columns(1)
                ->schema(fn(CustomForm $record)=>CustomFieldEditForm::getFieldAddActionSchema($record)),

            Group::make()
                ->columns(1)
                ->columnSpan(2)
                ->schema(fn(CustomForm $record)=>[
                    self::getCustomFieldRepeater($record)
                        ->saveRelationshipsUsing(fn($component, $state) =>CustomFormEditSave::saveCustomFields($component,$record,$state))
                        ->rules([CustomFormEditSave::getGeneralFieldRepeaterValidationRule()]),
                ]),
        ];
    }


    private static function getCustomFieldRepeater(CustomForm $record): Repeater {
        return Repeater::make("custom_fields")
            ->collapseAllAction(fn(Action $action)=> $action->hidden())
            ->expandAllAction(fn(Action $action)=> $action->hidden())
            ->relationship("customFieldInLayout")
            ->orderColumn("form_position")
            ->saveRelationshipsUsing(fn()=>empty(null))
            ->mutateRelationshipDataBeforeFillUsing(function($data) use ($record) {
                $data = CustomFieldEditForm::mutateOptionDatas($data, $record);
                $data = CustomFieldRuleEditForm::mutateRuleDatasOnLoad($data, $record);
                return $data;
            })
            ->collapsible(false)
            ->addable(false)
            ->defaultItems(0)
            ->columnSpan(2)
            ->persistCollapsed()
            ->label("")
            ->reorderable()
            ->collapsed()
            ->lazy()
            ->extraItemActions([
                self::getPullOutLayoutAction(),
                self::getPullInLayoutAction(),
                CustomFieldEditForm::getEditCustomFieldAction($record),
            ])
            ->itemLabel(function($state){
                $styleClasses = "text-sm font-medium ext-gray-950 dark:text-white truncate select-none";
                $type = self::getFieldTypeFromRawDate($state);
                $generalBadge= null;

                if(!empty($state["general_field_id"])){
                    $generalBadge = new HtmlBadge("Gen", Color::rgb("rgb(43, 164, 204)")); Blade::render('<x-filament::badge size="Gen">New</x-filament::badge>');
                    $genField = GeneralField::cached($state["general_field_id"]);
                    $name = $genField->name_de; //ToDo Translate
                    $icon = Blade::render('<x-'. $genField->icon .' class="h-4 w-4 "/>') ;
                }
                else  {
                    $name = $state["name_de"]; //ToDo Translate
                    $icon = Blade::render('<x-'. $type->icon() .' class="h-4 w-4 "/>') ;
                }

                $badgeCount =null;
                if($type instanceof CustomLayoutType){
                    $size = empty($state["custom_fields"])?0:sizeof($state["custom_fields"]);
                    $badgeCount = new HtmlBadge($size);
                    $span = '<span x-on:click.stop="isCollapsed = !isCollapsed" class="cursor-pointer flex" >';
                }
                else $span = '<span  class="cursor-pointer flex">';

                $h4 = '<h4 class="'.$styleClasses.'">';
                $html = "</h4>". $span;
                if(!is_null($badgeCount)) $html .= '<span class="px-1.5">'. $badgeCount. '</span>';
                if(!is_null($generalBadge)) $html .= '<span class="px-1.5">'. $generalBadge. '</span>';
                $html .= '<span class="px-1.5">' .$icon . '</span>'. $h4 . $name . " </h4></span><h4>";

                 return  new HtmlString($html);
               }
            )

            ->schema([
                Group::make()
                    ->visible(fn($state)=>self::getFieldTypeFromRawDate($state) instanceof CustomFieldType)
                    ->schema(function(Get $get,$state) use ($record) {
                        $type = self::getFieldTypeFromRawDate($state);
                        if($type instanceof CustomLayoutType)
                            return[self::getCustomFieldRepeater($record)];
                        else return [];
                    })
            ]);
    }




    public static function getUsedGeneralFieldIds(array $customFields):array {
        $usedGeneralFields = array_filter(
            array_values($customFields),
            fn($field)=> !empty($field["general_field_id"])
        );
        $nestedFields = collect(array_values($customFields))
            ->filter(fn($field)=> !empty($field["custom_fields"]))
            ->map(fn($field)=> $field["custom_fields"]);


        $usedGeneralFields=  array_filter($usedGeneralFields, fn($value)=> !is_null($value));

        if($nestedFields->count() > 0){
            $nestedGeneralFields = $nestedFields->map(fn(array $fields)=> self::getUsedGeneralFieldIds($fields))->flatten(1);
            return array_merge(array_map(fn($used) => $used["general_field_id"],$usedGeneralFields), $nestedGeneralFields->toArray());
        }

        return array_map(fn($used) => $used["general_field_id"],$usedGeneralFields);
    }



    private static function getPullInLayoutAction(): Action {
        return Action::make("pullIn")
            ->icon('heroicon-m-arrow-long-up')
            ->action(function(array $arguments,array $state, $set, Get $get){
                $itemIndex = $arguments["item"];
                $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
                $upperKey = array_keys($state)[$itemIndexPostion-1];

                $newUpperState = $get("custom_fields.$upperKey.custom_fields");
                $newUpperState[$itemIndex] =$state[$itemIndex];
                $set("custom_fields.$upperKey.custom_fields",$newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields" , $newState);

            })
            ->hidden(function($arguments,$state) {
                $itemIndex = $arguments["item"];
                $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
                if($itemIndexPostion == 0) return true;
                $upperCustomFieldData = $state[array_keys($state)[$itemIndexPostion-1]];
                $type = self::getFieldTypeFromRawDate($upperCustomFieldData);
                return !($type instanceof CustomLayoutType);
            });
    }

    private static function getPullOutLayoutAction(): Action {
        return Action::make("pullOut")
            ->icon('heroicon-m-arrow-long-left')
            ->action(function(array $arguments,array $state, $set, Get $get){
                $itemIndex = $arguments["item"];
                $newUpperState =  $get("../../custom_fields");

                $newUpperState[$itemIndex] =$state[$itemIndex];
                $set("../../custom_fields",$newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields" , $newState);

            })
            ->hidden(function($arguments,$state, $get) {
               return is_null($get("../../custom_fields"));
            });
    }

    public static function getFieldTypeFromRawDate(array $data): ?CustomFieldType {
        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        return $isGeneral? GeneralField::cached($data["general_field_id"])->getType(): CustomFieldType::getTypeFromName($data["type"]);
    }

    private static function getKeyPosition($key, $array):  int {
        $keys = array_keys($array);
        return array_search($key, $keys);
    }

}
