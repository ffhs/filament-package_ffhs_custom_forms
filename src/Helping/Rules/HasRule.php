<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventList;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerList;

trait HasRule
{
    public static function make(array|TriggerList $triggerData = [], array|EventList $eventData = []): static
    {
        $rule = new static();
        $rule->setTriggerData($triggerData)->setEventData($eventData);
        return $rule;
    }

    public function getEventData(): TriggerList{
        return EventList::make($this->getRawEventData());
    }
    public function getTriggerData(): EventList{
        return TriggerList::make($this->getRawTriggerData());
    }




}
