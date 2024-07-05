<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule\Translations\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use ReflectionClass;

class RequiredRuleType extends FieldRuleAbstractType
{
    use HasRuleTriggerPluginTranslate;
    public static function identifier(): string {
        return "is_required_rule";
    }

    public function getDisplayName(array $ruleData, Repeater $component, Get $get): string { //ToDo translate
        if($ruleData["rule_data"]["is_required_on_activation"])
            return "Feld nicht benötigt";
        return parent::getDisplayName($ruleData, $component, $get);
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Toggle::make("is_required_on_activation")
            ->label("Feld benötigt, falls die Regel nicht ausgeführt wird")// ToDo Translate
            ->hintIconTooltip("Bei Ja wird das Feld nicht benötigt, falls die Regel zuschlägt"); // ToDo Translate
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return !($type instanceof CustomLayoutType);
    }

    public function mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array {
        if(!array_key_exists("is_required_on_activation",$ruleData["rule_data"]))$ruleData["rule_data"]["is_required_on_activation"] = false;
        return $ruleData;
    }

    public function afterComponentRender(Component|\Filament\Infolists\Components\Component $component ,FieldRule $rule): Component|\Filament\Infolists\Components\Component {
        if(!($component instanceof Field)) return $component;
        $setting =  $rule->rule_data["is_required_on_activation"];

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty("isRequired");
        $property->setAccessible(true);
        $isRequiredOld = $property->getValue($component);

        return $component->required(function(Field $component) use ($isRequiredOld, $setting, $rule) {
            $anchor = $this->canRuleExecute($component,$rule);
            $isRequired = $setting?!$anchor:$anchor;
            if(!$isRequired) return $component->evaluate($isRequiredOld);
            return true;
        });
    }


    public function getCreateRuleData(): array {
        return ["is_required_on_activation"=>false];
    }
}
