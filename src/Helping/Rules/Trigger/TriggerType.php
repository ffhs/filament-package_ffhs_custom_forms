<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rule\RuleTrigger;

interface TriggerType extends Type
{
    public function isTrigger(array $arguments, mixed $target, RuleTrigger $rule): bool;

    public function getDisplayName(): string;

    public function getFormSchema(): array;

}
