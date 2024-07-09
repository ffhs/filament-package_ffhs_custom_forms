<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAnchorAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor\RuleEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\MaxWidth;

class EditFieldRuleModal extends Group
{
    protected function setUp(): void {

        parent::setUp();

        $this
            /*->schema(function($state){
                $type = CustomFieldUtils::getFieldTypeFromRawDate($state);

                return [
                    $this->getRuleRepeater($type),
                    $this->getRuleAddAction($type),
                ];
            });*/
        ->schema([
                    RuleEditor::make()
                ]);
    }


    protected function useRawFormData(Action $action): array
    {
        $forms = $action->getLivewire()->getCachedForms();
        return array_values($forms)[count($forms)-1]->getRawState();
    }


    protected function getRuleAddAction(CustomFieldType $type): ActionContainer
    {
        $action = Action::make("add_rule")
            ->action(fn($data, $get, $set)=> $set("rules", array_merge([uniqid()=> $data],$get("rules"))))
            ->fillForm(["anchor_data"=>[], "rule_data"=>[]])
            ->label("Regel hinzufügen"); //ToDo Translate

        return $this->prepareRuleAction($action, $type) ->toFormComponent();
    }

    protected function getEditRuleAction(CustomFieldType $type): Action {
           $action = Action::make("edit_rule")
                ->icon('heroicon-m-pencil-square')
                ->fillForm(fn($state,$arguments) => $state[$arguments["item"]])
                ->label("Regel hinzufügen")
                ->action(fn($data, $get, $set,$arguments) => dd("test") .$set("rules." . $arguments["item"], $data));

           return $this->prepareRuleAction($action, $type);

    }


    protected function prepareRuleAction(Action $action, CustomFieldType $type): Action
    {
        return $action ->modalWidth(MaxWidth::SixExtraLarge)
            ->form([FieldModalRuleEditorModal::make($type)])
            ->mutateFormDataUsing($this->useRawFormData(...));
    }

    protected function getRuleRepeater(CustomFieldType $type): Repeater {
        return Repeater::make("rules")
            ->collapseAllAction(fn(Action $action)=> $action->hidden())
            ->expandAllAction(fn(Action $action)=> $action->hidden())
            ->orderColumn("execution_order")
            ->itemLabel(function($state,$get,$component){
                $fieldRuleAnchorType = FieldRuleAnchorAbstractType::getTypeFromIdentifier($state["anchor_identifier"]);
                if($state["rule_identifier"] == null) return "Error";
                $fieldRuleType = FieldRuleAbstractType::getTypeFromIdentifier($state["rule_identifier"]);
                return $fieldRuleAnchorType->getDisplayName($state,$component,$get) . " --> " . $fieldRuleType->getDisplayName($state,$component,$get);
            })
            ->collapsible(false)
            ->addable(false)
            ->defaultItems(0)
            ->label("")
            ->collapsed()
            ->deletable()
            ->schema([])
            ->extraItemActions([$this->getEditRuleAction($type)]);
    }
}
