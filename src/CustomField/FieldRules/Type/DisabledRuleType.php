<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
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

class DisabledRuleType extends FieldRuleType
{
    use HasRulePluginTranslate;

    public static function identifier(): string {
        return "is_disabled_rule";
    }

    public function getDisplayName(array $ruleData, Repeater $component, Get $get): string { //ToDo translate
        if($ruleData["rule_data"]["is_disabled_on_activation"])
            return "Feld aktivieren";
        return parent::getDisplayName($ruleData, $component, $get);
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Toggle::make("is_disabled_on_activation")
            ->label("Deaktivieren des Feldes, falls die Regel nicht ausgeführt wird")// ToDo Translate
            ->hintIconTooltip("Bei Ja wird das Feld nicht deaktiviert, falls die Regel zuschlägt"); // ToDo Translate
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return !($type instanceof CustomLayoutType);
    }

    public function mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array {
        if(!array_key_exists("is_disabled_on_activation",$ruleData["rule_data"]))$ruleData["rule_data"]["is_disabled_on_activation"] = false;
        return $ruleData;
    }


    public function afterComponentRender(Component|\Filament\Infolists\Components\Component $component, FieldRule $rule): Component|\Filament\Infolists\Components\Component {
        if(!($component instanceof Component)) return $component;
        $setting =  $rule->rule_data["is_disabled_on_activation"];

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty("isDisabled");
        $property->setAccessible(true);
        $isDisabledOld = $property->getValue($component);
        $customField = $rule->customField;

        return $component->disabled(function(Component $component,$set) use ($isDisabledOld, $setting, $customField, $rule) {
            $anchor = $this->canRuleExecute($component,$rule);
            $disabled = $setting?!$anchor:$anchor;
            if(!$disabled) return $component->evaluate($isDisabledOld);
            $set(FormMapper::getIdentifyKey($customField),null);
            return true;
        });
    }


    public function getCreateRuleData(): array {
       return ["is_disabled_on_activation"=> false];
    }
}
