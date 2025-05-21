<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp;

use Closure;

trait UsePosSplit
{
    protected bool|Closure $usePoseSplit = false;
    protected array|Closure|null $poseSplit = [];


    //The array have to look like: [$beginPos,$endPos]
    public function usePoseSplit(bool|Closure $usePosSplit=true):static {
        $this->usePoseSplit = $usePosSplit;
        return $this;
    }

    public function poseSplit(array|Closure|null $posSplit):static {
        $this->poseSplit = $posSplit;
        return $this;
    }

    public function isUsePoseSplit(): bool{
        return $this->evaluate($this->usePoseSplit);
    }

    function loadPosTypeSplitAnswerData(mixed $answer): array {
        [$beginPos, $endPos] = $this->getPoseSpilt();
        return CustomFormLoadHelper::load($answer, $beginPos, $endPos);
    }

    public function getPoseSpilt(): ?array{
        if(!$this->isUseFieldSplit()) return null;
        return $this->evaluate($this->poseSplit);
    }


}
