<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasRulePluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class IsDisabledRuleType extends FieldRuleType
{
    use HasRulePluginTranslate;

    public static function identifier(): string {
        return "is_disabled_rule";
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Toggle::make("is_disabled_on_activation")
            ->label("Deaktivieren des Feldes, falls die Regel nicht ausgeführt wird")// ToDo Translate
            ->hintIconTooltip("Bei Nein wird das Feld nicht deaktiviert, falls die Regel nicht zuschlägt"); // ToDo Translate
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return !($type instanceof CustomLayoutType);
    }

    public function afterRender(Component|\Filament\Infolists\Components\Component $component ,CustomField $customField, FieldRule $rule): Component|\Filament\Infolists\Components\Component {
        if(!($component instanceof Component)) return $component;
        $setting =  $rule->rule_data["is_disabled_on_activation"];
        return $component->disabled(function(Component $component) use ($setting, $customField, $rule) {
            $anchor = $rule->getAnchorType()->canRuleExecute($component,$customField,$rule);
            return (!$anchor)?$setting:!$setting;
        });
    }


}
