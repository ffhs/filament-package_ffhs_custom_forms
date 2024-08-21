<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfoComponent;

class IsInfolistTrigger extends FormRuleTriggerType
{
    use HasRuleTriggerPluginTranslate;
    use HasFormTargets;

    public static function identifier(): string {
        return "infolist_view";
    }

    public function getFormSchema(): array
    {
        return [];
    }

    public function isTrigger(array $arguments, mixed $target, RuleTrigger $rule): bool
    {
        return $target instanceof InfoComponent;
    }
}
