<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal\Rule;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\UseComponentInjection;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class FieldModalRuleSection extends Section
{
    use UseComponentInjection;

    public static function make(string | array | Htmlable | Closure | null| CustomFieldType $heading = null): static {
        return self::injectIt($heading, ['heading' => "Regeln"]); //ToDo Translate
    }


    protected function setUp(): void {
        [$customForm, $type] = $this->injection;

        parent::setUp();

        $this->schema([
            $this->getRuleAddAction($customForm,$type),
            $this->getRuleRepeater($customForm,$type),
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
                ->mutateFormDataUsing(fn(Action $action) =>
                    array_values($action->getLivewire()->getCachedForms())[2]->getRawState()
                )

        ]);
    }

    protected function getRuleRepeater($form,$type): Repeater {
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
                    ->form([FieldModalRuleEditorModal::make($form,$type)])
                    ->label("Regel hinzufügen") //ToDo Translate
                    ->action(fn($data, $get, $set,$arguments)=> $set("rules." . $arguments["item"], $data))
                    ->mutateFormDataUsing(function(Action $action) {
                        return array_values($action->getLivewire()->getCachedForms())[2]->getRawState();
                    }),
            ]);
    }

}
