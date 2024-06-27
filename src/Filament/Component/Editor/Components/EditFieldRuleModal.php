<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\MaxWidth;

class EditFieldRuleModal extends Group
{

    protected function setUp(): void {

        parent::setUp();

        $this
            ->schema(fn($record, $state)=>[
                $this->getRuleRepeater($record, CustomFieldUtils::getFieldTypeFromRawDate($state)),
                $this->getRuleAddAction($record, CustomFieldUtils::getFieldTypeFromRawDate($state)),
            ]);
    }


    protected function getRuleAddAction(CustomForm $form, CustomFieldType $type): Actions {

        return Actions::make([
            Action::make("add-rule")
                ->action(fn($data, $get, $set)=> $set("rules", array_merge([uniqid()=> $data],$get("rules"))))
                ->form([FieldModalRuleEditorModal::make($form,$type)])
                ->fillForm(["anchor_data"=>[],"rule_data"=>[]])
                ->modalWidth(MaxWidth::SixExtraLarge)
                ->label("Regel hinzufügen") //ToDo Translate
                ->mutateFormDataUsing(function(Action $action) {
                    array_values($action->getLivewire()->getCachedForms())[2]->getRawState();
                })

        ]);
    }

    protected function getRuleRepeater(CustomForm $form, CustomFieldType $type): Repeater {
        return Repeater::make("rules")
            ->collapseAllAction(fn(Action $action)=> $action->hidden())
            ->expandAllAction(fn(Action $action)=> $action->hidden())
            ->orderColumn("execution_order")
            ->itemLabel(function($state,$get,$component){
                $fieldRuleAnchorType = FieldRuleAnchorType::getTypeFromIdentifier($state["anchor_identifier"]);
                if($state["rule_identifier"] == null) return "Error";
                $fieldRuleType = FieldRuleType::getTypeFromIdentifier($state["rule_identifier"]);
                return $fieldRuleAnchorType->getDisplayName($state,$component,$get) . " --> " . $fieldRuleType->getDisplayName($state,$component,$get);
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
                    ->form([FieldModalRuleEditorModal::make($form,$type)])
                    ->label("Regel hinzufügen") //ToDo Translate
                    ->action(fn($data, $get, $set,$arguments)=> $set("rules." . $arguments["item"], $data))
                    ->mutateFormDataUsing(function(Action $action) {
                        return array_values($action->getLivewire()->getCachedForms())[2]->getRawState();
                    }),
            ]);
    }
}
