<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\Helper\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

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

    function loadLayoutTypeSplitAnswerData(mixed $answer): array {
        $layoutField = $answer->customForm->customFieldsWithTemplateFields
            ->filter(fn(CustomField $field) => $field->getInheritState()["type"] == $this->getLayoutTypeSplit()::getFieldIdentifier())
            ->first();

        if (is_null($layoutField)) return [];
        if ($layoutField->form_position == $layoutField->layout_end_position) return [];

        return CustomFormLoadHelper::loadSplit($answer, $layoutField->form_position + 1,
            $layoutField->layout_end_position);
    }
}
