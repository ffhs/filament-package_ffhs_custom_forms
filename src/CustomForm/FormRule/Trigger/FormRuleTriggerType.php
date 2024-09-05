<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Forms\Components\Component;
use \Filament\Infolists\Components\Component as InfolistComponent;
abstract class FormRuleTriggerType implements TriggerType
{
    use IsType;

    public static function getConfigTypeList(): string
    {
        return "rules.triggers";
    }
    public function prepareComponents(array &$components, RuleTrigger $trigger): array
    {
        foreach ($components as $fieldIdentifier => $component) {
            $components[$fieldIdentifier] = $this->prepareComponent($component, $trigger);
        }

        return $components;
    }
    public function prepareComponent(Component|InfolistComponent $component, RuleTrigger $trigger): Component|InfolistComponent
    {
        return $component;
    }



}
