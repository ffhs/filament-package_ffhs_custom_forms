<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;

trait UseSplitCustomForm
{
    use UseLayoutSplit;
    use UseFieldSplit;
    use UsePosSplit;

    public function getFormSchema(EmbeddedCustomForm $component): array
    {
        if ($this->isUseLayoutTypeSplit()) {
            return $component->getLayoutTypeSplitFormSchema($component);
        }

        if ($this->isUseFieldSplit()) {
            return $component->getFieldSplitFormSchema($component);
        }

        if ($this->isUsePoseSplit()) {
            return $component->getPosSplitFormSchema($component);
        }

        return $component->getDefaultFormSchema($component);
    }

    public function getDefaultFormSchema(EmbeddedCustomForm $component): array
    {
        $customForm = $component->getCustomForm();
        if (is_null($customForm)) {
            return [];
        }

        return CustomFormRender::generateFormSchema(
            $customForm,
            $component->getViewMode()
        );
    }


    public function loadAnswerData(EmbeddedCustomForm $component): array
    {
        $record = $component->getCustomFormAnswer();

        if (is_null($record)) {
            return [];
        }

        if ($component->isUseLayoutTypeSplit()) {
            return $component->loadLayoutTypeSplitAnswerData($record);
        }

        if ($component->isUseFieldSplit()) {
            return $component->loadFieldTypeSplitAnswerData($record);
        }

        if ($component->isUsePoseSplit()) {
            return $component->loadPosTypeSplitAnswerData($record);
        }

        return $component->loadCustomAnswerData($record);
    }

}
