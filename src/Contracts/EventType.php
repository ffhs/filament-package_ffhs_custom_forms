<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;

interface EventType extends Type
{
    public function handle(Closure $triggers, array $arguments, mixed &$target, RuleEvent $rule): mixed;

    public function getDisplayName(): string;

    public function getFormSchema(): array;



}
