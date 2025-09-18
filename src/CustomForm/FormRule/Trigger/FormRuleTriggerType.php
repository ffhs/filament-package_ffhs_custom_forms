<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsType;
use Filament\Schemas\Components\Component;

abstract class FormRuleTriggerType implements TriggerType
{
    use IsType;

    public static function getConfigTypeList(): string
    {
        return 'rule.trigger';
    }

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

    public function mutateDataOnClone(array $data, CustomForm $target): array
    {
        return $data;
    }
}
