<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\FormFieldDisplayer;

trait UseSplitFormSchema
{
    use UseSplitCustomForm;
    use CanRenderSplitCustomForm;

    public function getFormSchema(EmbeddedCustomForm $component): array
    {
        if (is_null($component->getCustomForm())) {
            return [];
        }

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

    protected function getDefaultFormSchema(EmbeddedCustomForm $component): array
    {
        return $this->generateFormSchema(
            $component->getCustomForm(),
            $component->getViewMode()
        );
    }

    protected function getLayoutTypeSplitFormSchema(EmbeddedCustomForm $component): array
    {
        /**@var $customForm $customForm */
        $customForm = $component->getCustomForm();

        $viewMode = $component->getViewMode();
        $layoutType = $component->getLayoutTypeSplit();
        $displayer = FormFieldDisplayer::make($customForm);
        return $this->renderLayoutTypeSplit($layoutType, $customForm, $displayer, $viewMode);
    }

    protected function getPosSplitFormSchema(EmbeddedCustomForm $component): array
    {
        /**@var $customForm $customForm */
        $customForm = $component->getCustomForm();

        $viewMode = $component->getViewMode();
        $formEndPos = $this->getPoseSpiltEnd();
        $formBeginPos = $this->getPoseSpiltStart();
        $displayer = FormFieldDisplayer::make($customForm);
        return $this->renderPose($formBeginPos, $formEndPos, $customForm, $displayer, $viewMode);
    }

    protected function getFieldSplitFormSchema(EmbeddedCustomForm $component): array
    {
        /**@var $customForm $customForm */
        $customForm = $component->getCustomForm();

        $viewMode = $component->getViewMode();
        $field = $component->getFieldSplit();
        $displayer = FormFieldDisplayer::make($customForm);

        if (is_null($field)) {
            return [];
        }

        return $this->renderField($field, $customForm, $displayer, $viewMode);
    }
}
