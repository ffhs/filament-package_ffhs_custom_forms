<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

trait UseFieldSplit
{

    protected bool|Closure $useFieldSplit = false;
    protected null|CustomField $fieldSplit = null;

    public function useFieldSplit(bool|Closure $useFieldSplit=true):static {
        $this->useFieldSplit = $useFieldSplit;
        return $this;
    }

    public function fieldSplit(CustomField|Closure|null $fieldSplit):static {
        $this->fieldSplit = $fieldSplit;
        return $this;
    }

    public function isUseFieldSplit(): bool{
        return $this->evaluate($this->useFieldSplit);
    }

    public function getFieldSplit(): ?CustomField{
        return $this->evaluate($this->fieldSplit);
    }

    function loadFieldTypeSplitAnswerData(mixed $answer): array {
        $field = $this->getFieldSplit();
        if (is_null($field)) return [];
        if ($field->form_position == $field->layout_end_position) return [];

        return CustomFormLoadHelper::loadSplit($answer, $field->form_position + 1,
            $field->layout_end_position);
    }

}
