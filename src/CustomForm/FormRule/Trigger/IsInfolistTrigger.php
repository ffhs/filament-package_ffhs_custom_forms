<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Infolists\Components\Component;

class IsInfolistTrigger extends FormRuleTriggerType
{
    use HasRuleTriggerPluginTranslate;
    use HasTriggerEventFormTargets;

    public static function identifier(): string
    {
        return 'infolist_view';
    }

    public function getFormSchema(): array
    {
        return [];
    }

    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool
    {
        return (array_values($target)[0] ?? null) instanceof Component;
    }
}
