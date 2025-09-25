<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
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
        return $this->getCustomFormRelation() ?? $this->evaluate($this->customForm) ?? $this->evaluate($this->customFormData);
    }

}
