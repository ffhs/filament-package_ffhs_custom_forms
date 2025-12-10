<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;

class AlwaysRuleTrigger extends FormRuleTriggerType
{
    use HasTriggerEventFormTargets;
    use HasRuleTriggerPluginTranslate;

    public static function identifier(): string
    {
        return 'always';
    }

    public function isTrigger(array $arguments, mixed &$target, EmbedRuleTrigger $trigger): bool
    {
        return true;
    }

    public function getConfigurationSchema(): array
    {
        return [];
    }
}
