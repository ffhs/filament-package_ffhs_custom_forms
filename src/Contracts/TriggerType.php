<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;

interface TriggerType extends Type
{
    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool;

    public function getDisplayName(): string;

    public function getFormSchema(): array;
}
