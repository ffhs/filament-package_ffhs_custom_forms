<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Exeptions\UnexpectedRuleTargetType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfoComponent;

abstract class FieldRuleTriggerType implements TriggerType
{
    use IsType;

    public static function getConfigTypeList(): string
    {
        return "rules.triggers";
    }


    /**
     * @throws UnexpectedRuleTargetType
     */
    public final function isTrigger(array $arguments, mixed $target, FieldRule|Rule $rule): bool
    {
        $state = $arguments['state'];
        $rule = $arguments['rule'];
        $fieldKey = $arguments['field_key'];

        if($target instanceof InfoComponent) return $this->triggerOnInfolist($arguments, $target, $rule);
        if($target instanceof Component) return $this->triggerOnForm($arguments, $target, $rule);
        throw new UnexpectedRuleTargetType( [InfoComponent::class, Component::class], $target::class, $this::class);
    }


    protected function getFormModel(array $arguments): CustomForm
    {
        $record = $arguments['form'];
        if($record instanceof CustomFormAnswer) return $record->customForm;
        return $record;
    }

    protected function getFieldModel(array $arguments): CustomField
    {
        return $arguments['field'];
    }

    protected function getFormState(array $arguments): array
    {
        return $arguments['state'];
    }

    public abstract function triggerOnForm(array $arguments, Component $component, FieldRule $rule):bool;
    public abstract function triggerOnInfolist(array $arguments, InfoComponent $component, FieldRule $rule):bool;



    public abstract function getRuleEditComponents(): array;
}
