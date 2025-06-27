<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsType;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistsComponent;

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

    public function prepareComponent(
        FormsComponent|InfolistsComponent $component,
        RuleTrigger $trigger
    ): FormsComponent|InfolistsComponent {
        return $component;
    }

    public function mutateDataOnClone(array $data, CustomForm $target): array
    {
        return $data;
    }
}
