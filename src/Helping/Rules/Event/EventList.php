<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestingObject;

class EventList extends NestedFlattenList
{
    public function getType(): string
    {
        return EventType::class;
    }

}
