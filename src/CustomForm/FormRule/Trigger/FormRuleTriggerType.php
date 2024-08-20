<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;

abstract class FormRuleTriggerType implements TriggerType
{
    use IsType;

    public static function getConfigTypeList(): string
    {
        return "rules.triggers";
    }
}
