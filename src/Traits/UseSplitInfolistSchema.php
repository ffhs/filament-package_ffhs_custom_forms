<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\InfolistFieldDisplayer;

trait UseSplitInfolistSchema
{
    //ToDo improve
    use UseSplitCustomForm;
    use CanRenderSplitCustomForm;

    public function getCustomFormSchema(): array
    {
        if ($this->isUseLayoutTypeSplit()) {
            return $this->getSplitLayoutInfolistSchema();
        }

        if ($this->isUseFieldSplit()) {
            return $this->getSplitFieldInfolistSchema();
        }

        if ($this->isUsePoseSplit()) {
            return $this->getSplitPosInfolistSchema();
        }

        return $this->getDefaultInfolistSchema();
    }

    protected function getDefaultInfolistSchema(): array
    {
        return $this->generateInfoListSchema($this->getCustomFormAnswer(), $this->getViewMode());
    }

    protected function getSplitPosInfolistSchema(): array
    {
        $beginPos = $this->getPoseSpiltStart();
        $endPos = $this->getPoseSpiltEnd();

        $formAnswer = $this->getCustomFormAnswer();
        $viewMode = $this->getViewMode();
        $customForm = $formAnswer->customForm;
        $displayer = InfolistFieldDisplayer::make($formAnswer);
        return $this->renderPose($beginPos, $endPos, $customForm, $displayer, $viewMode);
    }


    protected function getSplitFieldInfolistSchema(): array
    {
        $customField = $this->getFieldSplit();
        if (is_null($customField)) {
            return [];
        }
        $formAnswer = $this->getCustomFormAnswer();
        $viewMode = $this->getViewMode();

        $customForm = $formAnswer->customForm;
        $displayer = InfolistFieldDisplayer::make($formAnswer);
        return $this->renderField($customField, $customForm, $displayer, $viewMode);
    }

    protected function getSplitLayoutInfolistSchema(): array
    {
        $layoutType = $this->getLayoutTypeSplit();
        $formAnswer = $this->getCustomFormAnswer();
        $viewMode = $this->getViewMode();
        $customForm = $formAnswer->customForm;
        $displayer = InfolistFieldDisplayer::make($formAnswer);
        return $this->renderLayoutTypeSplit($layoutType, $customForm, $displayer, $viewMode);
    }
}
