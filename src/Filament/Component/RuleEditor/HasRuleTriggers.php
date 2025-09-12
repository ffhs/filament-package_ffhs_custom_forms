<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;

trait HasRuleTriggers
{
    protected array|Closure|null $triggers;

    public function triggers(array|Closure|null $triggers): static
    {
        $this->triggers = $triggers;

        return $this;
    }

    public function getTrigger(?string $type): ?TriggerType
    {
        if (empty($type)) {
            return null;
        }

        return collect($this->getTriggers())
            ->filter(fn(TriggerType $event) => $event::identifier() === $type)
            ->first();
    }

    /**
     * @return TriggerType[]
     */
    public function getTriggers(): array
    {
        $types = $this->evaluate($this->triggers) ?? [];

        if (empty($types)) {
            return [];
        }

        if ($types[0] instanceof TriggerType) {
            return $types;
        }

        $finalTypes = [];

        foreach ($types as $typeClass) {
            $finalTypes[] = $typeClass::make();
        }

        return $finalTypes;
    }

}
