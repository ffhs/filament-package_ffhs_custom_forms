<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Forms\Components\Component;

abstract class FormRuleTriggerType implements TriggerType
{
    use IsType;

    public static function getConfigTypeList(): string
    {
        return "rules.triggers";
    }

    public function prepareComponent(Component $component, RuleTrigger $trigger): Component
    {
        return $component;
    }
}
