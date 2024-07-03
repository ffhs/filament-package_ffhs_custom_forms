<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestingObject;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;

class TriggerList extends NestedFlattenList
{
    public function getType(): string
    {
        return TriggerType::class;
    }

}
