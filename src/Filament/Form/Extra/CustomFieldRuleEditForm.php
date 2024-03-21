<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\EditCustomForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class CustomFieldRuleEditForm
{

    public static function getRuleComponent(CustomForm $customForm, CustomFieldType $type): Component {
        return  Section::make("Regeln")
            ->schema([
                self::getRuleAddAction($customForm,$type),
                self::getRuleRepeater($customForm,$type),
            ]);

        //toDo Rules Section
    }



    private static function getRuleAddAction(CustomForm $customForm, CustomFieldType $type): Actions {

        return Actions::make([
           Action::make("add-rule")
               ->label("Regel hinzufügen") //ToDo Translate
               ->form(self::getRuleEditSchema($customForm,$type))
               ->modalWidth(MaxWidth::SixExtraLarge)
               ->mutateFormDataUsing(fn(Action $action) =>
                    array_values($action->getLivewire()->getCachedForms())[2]->getRawState()
               )
               ->action(fn($data, $get, $set)=> $set("rules", array_merge([uniqid()=> $data],$get("rules")))),

        ]);
    }

    private static function getRuleEditSchema(CustomForm $customForm, CustomFieldType $type): array {
        $rules = self::getSelectableRules($customForm, $type);
        $anchors = self::getSelectableAnchors($customForm,$type);

        return [
            TextInput::make("rule_name")
                ->label("Regelname") //ToDo Translate
                ->required(),
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
                                ->live(),
                            Group::make()
                                ->statePath("anchor_data")
                                ->columnStart(1)
                                ->columnSpanFull()
                                ->columns()
                                ->schema(function($get, EditCustomForm $livewire) use ($customForm) {
                                    if(is_null($get("anchor_identifier"))) return [];
                                    $anchor = FieldRuleAnchorType::getAnchorFromName($get("anchor_identifier"));
                                    return [$anchor->settingsComponent($customForm, $livewire->data["custom_fields"])];
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
                                ->live(),

                            Group::make()
                                ->statePath("rule_data")
                                ->schema(function($get, EditCustomForm $livewire) use ($customForm) {
                                    if(is_null($get("rule_identifier"))) return [];
                                    $rule = FieldRuleType::getRuleFromName($get("rule_identifier"));
                                    return [$rule->settingsComponent($customForm, $livewire->data["custom_fields"])];
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

    /**
     * @param  CustomForm  $customForm
     * @param  CustomFieldType  $type
     * @return array
     */
    private static function getSelectableRules(CustomForm $customForm, CustomFieldType $type): array {
        $allRules = $customForm->getFormConfiguration()::typeRules();
        $rules = [];
        foreach ($allRules as $ruleClass) {
            /**@var FieldRuleType $rule */
            $rule = new $ruleClass();
            if (!$rule->canAddOnField($type)) continue;
            $rules[$rule->identifier()] = $rule->getTranslatedName();
        }
        return $rules;
    }

    /**
     * @param  CustomForm  $customForm
     * @return array
     */
    private static function getSelectableAnchors(CustomForm $customForm, CustomFieldType $type): array {
        $allAnchors = $customForm->getFormConfiguration()::typeRuleAnchors();
        $anchors = [];
        foreach ($allAnchors as $anchorClass) {
            /**@var FieldRuleAnchorType $anchor */
            $anchor = new $anchorClass();
            if (!$anchor->canAddOnField($type)) continue;
            $anchors[$anchor->identifier()] = $anchor->getTranslatedName();
        }
        return $anchors;
    }

    public static function mutateRuleDatasOnLoad(array $data, CustomForm $customForm): array {
        $data["rules"] = [];
        /**@var CustomField $customField*/
        $customField = $customForm->customFields->where("id",$data["id"])->first();

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
