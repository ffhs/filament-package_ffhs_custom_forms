<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\FormRule\Translations\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Infolists\Components\Component as InfoComponent;

class IsInfolistTrigger extends FormRuleTriggerType
{
    use HasRuleTriggerPluginTranslate;
    use HasTriggerEventFormTargets;

    public static function identifier(): string
    {
        return "infolist_view";
    }

    public function getFormSchema(): array
    {
        return [];
    }


    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool
    {
        return (array_values($target)[0] ?? null) instanceof InfoComponent;
    }
}
