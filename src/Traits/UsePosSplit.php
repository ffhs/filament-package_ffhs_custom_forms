<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\SplitCustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

trait UsePosSplit
{
    use CanLoadFormAnswerer;

    protected bool|Closure $usePoseSplit = false;
    protected Closure|null $poseSplitStart = null;
    protected Closure|null $poseSplitEnd = null;

    public function usePoseSplit(bool|Closure $usePosSplit = true): static
    {
        $this->usePoseSplit = $usePosSplit;
        return $this;
    }

    public function poseSplitStart(int|Closure|null $poseSplitStart): static
    {
        $this->poseSplitStart = $poseSplitStart;
        return $this;
    }

    public function poseSplitEnd(int|Closure|null $poseSplitEnd): static
    {
        $this->poseSplitEnd = $poseSplitEnd;
        return $this;
    }

    public function isUsePoseSplit(): bool
    {
        return $this->evaluate($this->usePoseSplit);
    }

    public function loadPosTypeSplitAnswerData(CustomFormAnswer $answer): array
    {
        $beginPos = $this->getPoseSpiltStart();
        $endPos = $this->getPoseSpiltEnd();
        return $this->loadCustomAnswerData($answer, $beginPos, $endPos);
    }

    public function getPoseSpiltStart(): ?int
    {
        if (!$this->isUseFieldSplit()) {
            return null;
        }
        return $this->evaluate($this->poseSplitStart);
    }

    public function getPoseSpiltEnd(): ?int
    {
        if (!$this->isUseFieldSplit()) {
            return null;
        }
        return $this->evaluate($this->poseSplitEnd);
    }

    public function getPosSplitFormSchema(EmbeddedCustomForm $component): array
    {
        $customForm = $component->getCustomForm();
        if (is_null($customForm)) {
            return [];
        }

        return
            SplitCustomFormRender::renderFormPose(
                $this->getPoseSpiltStart(),
                $this->getPoseSpiltEnd(),
                $customForm,
                $component->getViewMode()
            );
    }
}
