<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasRulePluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Toggle;
use Livewire\Livewire;

class RequiredRuleType extends FieldRuleType
{
    use HasRulePluginTranslate;
    public static function identifier(): string {
        return "is_required_rule";
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Toggle::make("is_required_on_activation")
            ->label("Feld benötigt, falls die Regel nicht ausgeführt wird")// ToDo Translate
            ->hintIconTooltip("Bei Ja wird das Feld nicht benötigt, falls die Regel zuschlägt"); // ToDo Translate
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return !($type instanceof CustomLayoutType);
    }

    public function afterRender(Component|\Filament\Infolists\Components\Component $component ,CustomField $customField, FieldRule $rule): Component|\Filament\Infolists\Components\Component {
        if(!($component instanceof Field)) return $component;
        $setting =  $rule->rule_data["is_required_on_activation"];
        return $component->required(function(Field $component) use ($setting, $customField, $rule) {
            return $rule->getAnchorType()->canRuleExecute($component,$customField,$rule)?!$setting:$setting;
        });
    }



}
