<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\Type;

interface TriggerType extends Type
{
    public function isTrigger(array $arguments, mixed $target, Rule $rule): bool;

    public function getDisplayName(): string;

}
