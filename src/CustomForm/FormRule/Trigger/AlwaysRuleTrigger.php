<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;

class AlwaysRuleTrigger extends FormRuleTriggerType
{
    use HasFormTargets;
    use HasRuleTriggerPluginTranslate;

    public static function identifier(): string {
        return "always";
    }

    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool
    {
        return true;
    }

    public function getFormSchema(): array
    {
        return  [];
    }
}
