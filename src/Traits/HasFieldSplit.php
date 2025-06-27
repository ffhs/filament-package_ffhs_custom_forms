<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

trait HasFieldSplit
{
    use CanLoadFormAnswer;

    protected bool|Closure $useFieldSplit = false;
    protected null|CustomField $fieldSplit = null;

    public function useFieldSplit(bool|Closure $useFieldSplit = true): static
    {
        $this->useFieldSplit = $useFieldSplit;

        return $this;
    }

    public function fieldSplit(CustomField|Closure|null $fieldSplit): static
    {
        $this->fieldSplit = $fieldSplit;

        return $this;
    }

    public function isUseFieldSplit(): bool
    {
        return $this->evaluate($this->useFieldSplit);
    }

    public function loadFieldTypeSplitAnswerData(CustomFormAnswer $answer): array
    {
        $field = $this->getFieldSplit();

        if (is_null($field) || $field->form_position === $field->layout_end_position) {
            return [];
        }

        $beginPos = $field->form_position + 1;
        $endPos = $field->layout_end_position;

        return $this->loadCustomAnswerData($answer, $beginPos, $endPos);
    }

    public function getFieldSplit(): ?CustomField
    {
        return $this->evaluate($this->fieldSplit);
    }
}
