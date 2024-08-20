<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\Type;

interface EventType extends Type
{
    public function handle(bool $triggered, array $arguments, mixed $target, Rule $rule): mixed;

    public function getDisplayName(): string;

    public function getFormSchema(): array;



}
