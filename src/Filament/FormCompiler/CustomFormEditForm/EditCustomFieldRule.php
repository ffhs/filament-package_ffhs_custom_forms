<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\MaxWidth;

class EditCustomFieldRule
{

    public static function getRuleComponent(CustomForm $customForm, CustomFieldType $type): Component {
        return  Section::make("Regeln")
            ->schema([
                self::getRuleAddAction($customForm,$type),
                self::getRuleRepeater($customForm,$type),
            ]);
    }



    private static function getRuleAddAction(CustomForm $customForm, CustomFieldType $type): Actions {

        return Actions::make([
           Action::make("add-rule")
               ->action(fn($data, $get, $set)=> $set("rules", array_merge([uniqid()=> $data],$get("rules"))))
               ->form(self::getRuleEditSchema($customForm,$type))
               ->fillForm(["anchor_data"=>[],"rule_data"=>[]])
               ->modalWidth(MaxWidth::SixExtraLarge)
               ->label("Regel hinzufügen") //ToDo Translate
               ->mutateFormDataUsing(fn(Action $action) =>
                    array_values($action->getLivewire()->getCachedForms())[2]->getRawState()
               )

        ]);
    }


    private static function getRuleEditSchema(CustomForm $customForm, CustomFieldType $type): array {
        $rules = self::getSelectableRules($customForm, $type);
        $anchors = self::getSelectableAnchors($customForm,$type);

        return [
            Group::make()
                ->columns()
                ->schema([
                    Section::make("Anker")//ToDo Translate
                        ->columnSpan(1)
                        ->schema([
                            Select::make("anchor_identifier")
                                ->disabled(fn($get)=> !is_null($get("anchor_identifier")))
                                ->label("Regel") //ToDo Translate
                                ->options($anchors)
                                ->required()
                                ->afterStateUpdated(function ($state,$set){
                                    if(is_null($state)) return;
                                    $set("anchor_data", FieldRuleAnchorType::getAnchorFromName($state)?->getCreateAnchorData());
                                })
                                ->live(),
                            Group::make()
                                ->statePath("anchor_data")
                                ->columnStart(1)
                                ->columnSpanFull()
                                ->columns()
                                ->schema(function($get,  $livewire) use ($customForm) {
                                    if(is_null($get("anchor_identifier"))) return [];
                                    $data = $livewire->data;

                                    $data = CustomFieldUtils::flattDownToCustomFields($data);

                                    $anchor = FieldRuleAnchorType::getAnchorFromName($get("anchor_identifier"));
                                    return [$anchor->settingsComponent($customForm, $data["custom_fields"])];
                                }),
                        ]),
                    Section::make("Regel")
                        ->columnSpan(1)
                        ->schema([
                            Select::make("rule_identifier")
                                ->disabled(fn($get)=> !is_null($get("rule_identifier")))
                                ->label("Regel") //ToDo Translate
                                ->options($rules)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state,$set){
                                    if(is_null($state)) return;
                                    $set("rule_data", FieldRuleType::getRuleFromName($state)?->getCreateRuleData());
                                }),
                            Group::make()
                                ->statePath("rule_data")
                                ->schema(function($get, $livewire) use ($customForm) {
                                    if(is_null($get("rule_identifier"))) return [];
                                    $data = $livewire->data;

                                    $data = CustomFieldUtils::flattDownToCustomFields($data);
                                    $rule = FieldRuleType::getRuleFromName($get("rule_identifier"));
                                    return [$rule->settingsComponent($customForm, $data["custom_fields"])];
                                }),
                        ]),
                ]),
        ];
    }

    private static function getRuleRepeater($customForm,$type): Repeater {
        return Repeater::make("rules")
            ->collapseAllAction(fn(Action $action)=> $action->hidden())
            ->expandAllAction(fn(Action $action)=> $action->hidden())
            ->orderColumn("execution_order")
            ->itemLabel(function($state){
                $fieldRuleAnchorType = FieldRuleAnchorType::getAnchorFromName($state["anchor_identifier"]);
                if($state["rule_identifier"] == null) return "Error";
                $fieldRuleType = FieldRuleType::getRuleFromName($state["rule_identifier"]);

                return $fieldRuleAnchorType->getTranslatedName() . " ==> " . $fieldRuleType->getTranslatedName();
            })
            ->collapsible(false)
            ->addable(false)
            ->defaultItems(0)
            ->label("")
            ->collapsed()
            ->deletable()
            ->schema([])
            ->extraItemActions([
                Action::make("edit-rule")
                    ->icon('heroicon-m-pencil-square')
                    ->fillForm(fn($state,$arguments) => $state[$arguments["item"]])
                    ->modalWidth(MaxWidth::SixExtraLarge)
                    ->form(self::getRuleEditSchema($customForm,$type))
                    ->label("Regel hinzufügen") //ToDo Translate
                    ->action(fn($data, $get, $set,$arguments)=>
                        $set("rules." . $arguments["item"], $data)
                    )
                    ->mutateFormDataUsing(fn(Action $action) =>
                        array_values($action->getLivewire()->getCachedForms())[2]->getRawState()
                    ),
            ]);
    }


    private static function getSelectableRules(CustomForm $customForm, CustomFieldType $type): array {
        $allRules = $type->overwrittenRules();
        if(is_null($allRules)) $allRules = $customForm->getFormConfiguration()::ruleTypes();
        $rules = [];
        foreach ($allRules as $ruleClass) {
            /**@var FieldRuleType $rule */
            $rule = new $ruleClass();
            if (!$rule->canAddOnField($type)) continue;
            $rules[$rule->identifier()] = $rule->getTranslatedName();
        }
        return $rules;
    }


    private static function getSelectableAnchors(CustomForm $customForm, CustomFieldType $type): array {
        $allAnchors = $type->overwrittenAnchorRules();
        if(is_null($allAnchors)) $allAnchors = $customForm->getFormConfiguration()::anchorRuleTypes();
        $anchors = [];
        foreach ($allAnchors as $anchorClass) {
            /**@var FieldRuleAnchorType $anchor */
            $anchor = new $anchorClass();
            if (!$anchor->canAddOnField($type)) continue;
            $anchors[$anchor->identifier()] = $anchor->getTranslatedName();
        }
        return $anchors;
    }

    public static function mutateRuleDataOnLoad(array $data, CustomForm $customForm): array {
        $data["rules"] = [];
        /**@var CustomField $customField*/
        $customField = $customForm->customFields->where("id",$data["id"])->first();
        if(is_null($customField)) return $data;
        foreach ($customField->fieldRules as $rule){
            /**@var FieldRule $rule*/
            $ruleData = $rule->toArray();
            $ruleData = $rule->getRuleType()->mutateDataBeforeLoadInEdit($ruleData,$rule) ;
            $ruleData =   $rule->getAnchorType()->mutateDataBeforeLoadInEdit($ruleData,$rule);
            $data["rules"][] = $ruleData;
        }

        return $data;
    }







}
