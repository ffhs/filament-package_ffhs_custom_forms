<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Toggle;

class IsRequiredRuleType extends FieldRuleType
{

    public static function identifier() {
        return "is_required_rule";
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Toggle::make("is_required_on_activation")
            ->label("Benötigt falls Regel ausgeführt wird")// ToDo Translate
            ->hintIconTooltip("Bei Nein wird das Feld benötigt falls die Regel nicht zuschlägt"); // ToDo Translate
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return !($type instanceof CustomLayoutType);
    }

    public function afterRender(bool $anchorActive, Component $component, FieldRule $rule, CustomFieldAnswer $answer): Component {
        if(!($component instanceof Field)) return $component;
        $setting =  $rule->rule_type_data["is_required_on_activation"];
        return $component->required($anchorActive?$setting:!$setting);
    }


}
