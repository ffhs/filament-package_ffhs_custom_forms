<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventList;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerList;

interface Rule
{
    public static function make(array|TriggerList $triggerData = [], array|EventList $eventData = []): static;

    public function getEventData(): TriggerList;
    public function getTriggerData(): EventList;

    public function getRawEventData(): array;
    public function getRawTriggerData(): array;


    public function setTriggerData(array|TriggerList $data): static;
    public function setEventData(array|EventList $data): static;


    public function handle(array $arguments, mixed $target): mixed;
}
