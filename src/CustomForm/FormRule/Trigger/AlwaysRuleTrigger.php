<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;

class AlwaysRuleTrigger extends FormRuleTriggerType
{
    use HasTriggerEventFormTargets;
    use HasRuleTriggerPluginTranslate;

    public static function identifier(): string
    {
        return "always";
    }

    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool
    {
        return true;
    }

    public function getFormSchema(): array
    {
        return [];
    }
}
