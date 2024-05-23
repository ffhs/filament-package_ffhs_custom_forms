<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasRulePluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use ReflectionClass;

class HiddenRuleType extends FieldRuleType
{
    use HasRulePluginTranslate;

    public static function identifier(): string {
        return "is_hidden_rule";
    }

    public function getDisplayName(array $ruleData, Repeater $component, Get $get): string { //ToDo translate
        if($ruleData["rule_data"]["is_hidden_on_activation"])
            return "Feld anzeigen";
        return parent::getDisplayName($ruleData, $component, $get);
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Toggle::make("is_hidden_on_activation")
            ->label("Feld anzeigen, falls die Regel ausgeführt wird")// ToDo Translate
            ->hintIconTooltip("Bei Ja wird das Feld nicht versteckt, falls die Regel zuschlägt"); // ToDo Translate
    }


    public function mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array {
        if(!array_key_exists("is_hidden_on_activation",$ruleData["rule_data"]))$ruleData["rule_data"]["is_hidden_on_activation"] = false;
        return $ruleData;
    }

    public function afterComponentRender(Component|\Filament\Infolists\Components\Component $component ,FieldRule $rule): Component|\Filament\Infolists\Components\Component {
        if(!($component instanceof Component)) return $component;
        $setting =  $rule->rule_data["is_hidden_on_activation"];

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty("isHidden");
        $property->setAccessible(true);
        $isHiddenOld = $property->getValue($component);
        $customField = $rule->customField;

        return $component->hidden(function(Component $component,$set, $get) use ($customField, $isHiddenOld, $setting, $rule) {
            $anchor = $rule->getAnchorType()->canRuleExecute($component, $rule);
            $hidden = $setting?!$anchor:$anchor;
            if(!$hidden) return $component->evaluate($isHiddenOld);
             $set(FormMapper::getIdentifyKey($customField), null);
            return true;
        });
    }


    public function getCreateRuleData(): array {
       return ["is_hidden_on_activation"=>false];
    }
}
