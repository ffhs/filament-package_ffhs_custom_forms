<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

trait UseLayoutSplit
{
    protected bool|Closure $useLayoutTypeSplit = false;
    protected CustomLayoutType|Closure|null $layoutTypeSplit = null;


    public function useLayoutTypeSplit(bool|Closure $useLayoutTypeSplit = true):static {
        $this->useLayoutTypeSplit = $useLayoutTypeSplit;
        return $this;
    }

    public function layoutTypeSplit(CustomLayoutType|Closure|null $layoutTypeSplit):static {
        $this->layoutTypeSplit = $layoutTypeSplit;
        return $this;
    }

    public function isUseLayoutTypeSplit(): bool{
        return $this->evaluate($this->useLayoutTypeSplit);
    }

    public function getLayoutTypeSplit(): CustomLayoutType{
        return $this->evaluate($this->layoutTypeSplit);
    }

    function loadLayoutTypeSplitAnswerData(CustomFormAnswer $answer): array {
        $layoutField = $answer->customForm->customFields
            ->filter(fn(CustomField $field) => $field->type == $this->getLayoutTypeSplit()::identifier())
            ->first();

        if (is_null($layoutField)) return [];
        if ($layoutField->form_position == $layoutField->layout_end_position) return [];

        return CustomFormLoadHelper::loadSplit($answer, $layoutField->form_position + 1, $layoutField->layout_end_position);
    }
}
