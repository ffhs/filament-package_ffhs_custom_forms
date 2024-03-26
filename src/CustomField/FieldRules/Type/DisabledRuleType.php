<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasRulePluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class DisabledRuleType extends FieldRuleType
{
    use HasRulePluginTranslate;

    public static function identifier(): string {
        return "is_disabled_rule";
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
        if(!array_key_exists("",$ruleData["rule_data"]))$ruleData["rule_data"]["is_disabled_on_activation"] = false;
        return $ruleData;
    }


    public function afterRender(Component|\Filament\Infolists\Components\Component $component ,CustomField $customField, FieldRule $rule): Component|\Filament\Infolists\Components\Component {
        if(!($component instanceof Component)) return $component;
        $setting =  $rule->rule_data["is_disabled_on_activation"];
        return $component->disabled(function(Component $component,$set) use ($setting, $customField, $rule) {
            $anchor = $rule->getAnchorType()->canRuleExecute($component,$customField,$rule);
            $disabled = $anchor?!$setting:$setting;
            if($disabled) $set(FormMapper::getIdentifyKey($customField),null);
            return $disabled;
        });
    }


}
