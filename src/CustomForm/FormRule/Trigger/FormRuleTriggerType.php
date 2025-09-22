<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FfhsUtils\Contracts\Rules\TriggerType;
use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FfhsUtils\Traits\Rules\IsTriggerType;
use Filament\Schemas\Components\Component;

abstract class FormRuleTriggerType implements TriggerType
{
    use IsTriggerType;

    public function prepareComponents(array &$components, RuleTrigger $trigger): array
    {
        foreach ($components as $fieldIdentifier => $component) {
            $components[$fieldIdentifier] = $this->prepareComponent($component, $trigger);
        }

        return $components;
    }

    public function prepareComponent(Component $component, RuleTrigger $trigger): Component
    {
        return $component;
    }

    public function mutateDataOnClone(array $data): array
    {
        return $data;
    }
}
