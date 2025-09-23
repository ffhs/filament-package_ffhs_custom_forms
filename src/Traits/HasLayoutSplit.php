<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

trait HasLayoutSplit
{
    use CanLoadFormAnswer;

    protected bool|Closure $useLayoutTypeSplit = false;
    protected CustomLayoutType|Closure|null $layoutTypeSplit = null;

    public function useLayoutTypeSplit(bool|Closure $useLayoutTypeSplit = true): static
    {
        $this->useLayoutTypeSplit = $useLayoutTypeSplit;

        return $this;
    }

    public function layoutTypeSplit(CustomLayoutType|Closure|null $layoutTypeSplit): static
    {
        $this->useLayoutTypeSplit(!is_null($layoutTypeSplit));
        $this->layoutTypeSplit = $layoutTypeSplit;

        return $this;
    }

    public function isUseLayoutTypeSplit(): bool
    {
        return $this->evaluate($this->useLayoutTypeSplit);
    }

    public function getLayoutTypeSplit(): CustomLayoutType
    {
        return $this->evaluate($this->layoutTypeSplit);
    }

    public function loadLayoutTypeSplitAnswerData(EmbedCustomFormAnswer $answer): array
    {
        $layoutField = $answer
            ->customForm
            ->customFields
            ->filter(fn(CustomField $field) => $field->type === $this->getLayoutTypeSplit()::identifier())
            ->first();

        if (is_null($layoutField) || $layoutField->form_position === $layoutField->layout_end_position) {
            return [];
        }

        $beginPos = $layoutField->form_position + 1;
        $endPos = $layoutField->layout_end_position;

        return $this->loadCustomAnswerData($answer, $beginPos, $endPos);
    }
}
