<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\SplitCustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

trait UseLayoutSplit
{
    use CanLoadFormAnswerer;

    protected bool|Closure $useLayoutTypeSplit = false;
    protected CustomLayoutType|Closure|null $layoutTypeSplit = null;

    public function useLayoutTypeSplit(bool|Closure $useLayoutTypeSplit = true): static
    {
        $this->useLayoutTypeSplit = $useLayoutTypeSplit;
        return $this;
    }

    public function layoutTypeSplit(CustomLayoutType|Closure|null $layoutTypeSplit): static
    {
        $this->layoutTypeSplit = $layoutTypeSplit;
        return $this;
    }

    public function isUseLayoutTypeSplit(): bool
    {
        return $this->evaluate($this->useLayoutTypeSplit);
    }

    public function loadLayoutTypeSplitAnswerData(CustomFormAnswer $answer): array
    {
        $layoutField = $answer->customForm->customFields
            ->filter(fn(CustomField $field) => $field->type === $this->getLayoutTypeSplit()::identifier())
            ->first();

        if (is_null($layoutField)) {
            return [];
        }
        if ($layoutField->form_position === $layoutField->layout_end_position) {
            return [];
        }

        $beginPos = $layoutField->form_position + 1;
        $endPos = $layoutField->layout_end_position;

        return $this->loadCustomAnswerData($answer, $beginPos, $endPos);
    }

    public function getLayoutTypeSplit(): CustomLayoutType
    {
        return $this->evaluate($this->layoutTypeSplit);
    }

    public function getLayoutTypeSplitFormSchema(EmbeddedCustomForm $component): array
    {
        $customForm = $component->getCustomForm();
        if (is_null($customForm)) {
            return [];
        }

        return SplitCustomFormRender::renderFormLayoutType(
            $component->getLayoutTypeSplit(),
            $customForm,
            $component->getViewMode()
        );
    }
}
