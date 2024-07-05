<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Exeptions\UnexpectedRuleTargetType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfoComponent;

abstract class FieldRuleTriggerType implements TriggerType
{
    use IsType;
    use HasFieldRuleType;

    public static function getConfigTypeList(): string
    {
        return "rules.triggers";
    }


    /**
     * @throws UnexpectedRuleTargetType
     */
    public final function isTrigger(array $arguments, mixed $target, FieldRule|Rule $rule): bool
    {
        if($target instanceof InfoComponent) return $this->triggerOnInfolist($arguments, $target, $rule);
        if($target instanceof Component) return $this->triggerOnForm($arguments, $target, $rule);
        throw new UnexpectedRuleTargetType( [InfoComponent::class, Component::class], $target::class, $this::class);
    }


    public abstract function triggerOnForm(array $arguments, Component $component, FieldRule $rule):bool;
    public abstract function triggerOnInfolist(array $arguments, InfoComponent $component, FieldRule $rule):bool;



}
