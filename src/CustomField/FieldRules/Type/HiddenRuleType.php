<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasRulePluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class HiddenRuleType extends FieldRuleType
{
    use HasRulePluginTranslate;

    public static function identifier(): string {
        return "is_hidden_rule";
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Toggle::make("is_hidden_on_activation")
            ->label("Feld verstecken, falls die Regel nicht ausgeführt wird")// ToDo Translate
            ->hintIconTooltip("Bei Ja wird das Feld nicht versteckt, falls die Regel zuschlägt"); // ToDo Translate
    }


    public function mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array {
        if(!array_key_exists("is_hidden_on_activation",$ruleData["rule_data"]))$ruleData["rule_data"]["is_hidden_on_activation"] = false;
        return $ruleData;
    }

    public function afterRender(Component|\Filament\Infolists\Components\Component $component ,CustomField $customField, FieldRule $rule): Component|\Filament\Infolists\Components\Component {
        if(!($component instanceof Component)) return $component;
        $setting =  $rule->rule_data["is_hidden_on_activation"];
        return $component->hidden(function(Component $component,$set) use ($setting, $customField, $rule) {
            $anchor = $rule->getAnchorType()->canRuleExecute($component,$customField,$rule);
            $hidden = $anchor?!$setting:$setting;
            if($hidden) $set(FormMapper::getIdentifyKey($customField),null);
            return $hidden;
        });
    }


}
