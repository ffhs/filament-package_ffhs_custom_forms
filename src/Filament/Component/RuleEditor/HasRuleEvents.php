<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;

trait HasRuleEvents
{
    protected Closure|array|null $events;

    public function events(array|Closure|null $events): static
    {
        $this->events = $events;

        return $this;
    }

    public function getEvent($type): ?EventType
    {
        return collect($this->getEvents())
            ->filter(fn(EventType $event) => $event::identifier() === $type)
            ->first();
    }

    public function getEvents(): array
    {
        $types = $this->evaluate($this->events) ?? [];

        if (empty($types)) {
            return [];
        }

        if ($types[0] instanceof EventType) {
            return $types;
        }

        $finalTypes = [];
        foreach ($types as $typeClass) {
            $finalTypes[] = $typeClass::make();
        }

        return $finalTypes;
    }

}
