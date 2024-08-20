<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rule\RuleEvent;

interface EventType extends Type
{
    public function handle(bool $triggered, array $arguments, mixed $target, RuleEvent $rule): mixed;

    public function getDisplayName(): string;

    public function getFormSchema(): array;



}
