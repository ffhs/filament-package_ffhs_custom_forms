<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FfhsUtils\Models\RuleEvent;
use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait HasCustomFormData
{
    use CanLoadCustomFormEditorData;

    protected EmbedCustomForm|Closure $customFormData;
    protected CustomForm|null|Closure $customForm = null;
    protected string|null|Closure $customFormRelation = null;
    protected null|CustomForm $cachedCustomFormRelation = null;

    public function customFormData(array|Closure $customFormData): static
    {
        $this->customFormData = $customFormData;
        return $this;
    }

    public function customForm(CustomForm|null|Closure $customForm): static
    {
        $this->customForm = $customForm;
        return $this;
    }

    public function customFormRelation(string|null|Closure $customFormRelation): static
    {
        $this->customFormRelation = $customFormRelation;
        return $this;
    }

    public function getCustomFormRelation(): ?CustomForm
    {
        if ($this->cachedCustomFormRelation) {
            return $this->cachedCustomFormRelation;
        }

        $relationshipName = $this->evaluate($this->customFormRelation);

        if (!$relationshipName) {
            return null;
        }

        $parentRecord = $this->getRecord();

        if (!$parentRecord) {
            return null;
        }

        if ($parentRecord->relationLoaded($relationshipName)) {
            $record = $parentRecord->getRelationValue($relationshipName);
        } else {
            $record = $parentRecord->{$relationshipName};
        }

        if (!$record?->exists) {
            return null;
        }

        return $this->cachedCustomFormRelation = $record;
    }

    public function getCustomForm(): ?EmbedCustomForm
    {
        $form = $this->getCustomFormRelation() ?? $this->evaluate($this->customForm) ?? $this->evaluate($this->customFormData);

        if (!$form instanceof CustomForm) {
            return $form;
        }

        if (!$form->rules->first()->relationLoaded('ruleTriggers') || !$form->rules->first()->relationLoaded('ruleEvents')) {
            $form->rules->loadExists(['ruleTriggers', 'ruleEvents']);
        }

        $form->rules->each(function (Rule $rule) {
            $rule->ruleTriggers->each(fn(RuleTrigger $ruleEvent) => $ruleEvent->setRelation('rule', $rule));
            $rule->ruleEvents->each(fn(RuleEvent $ruleEvent) => $ruleEvent->setRelation('rule', $rule));
        });


        return $form;
    }

}
