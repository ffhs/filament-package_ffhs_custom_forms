<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\Helper\CustomFormLoadHelper;

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

    public function getPoseSpilt(): ?array{
        return $this->evaluate($this->poseSplit);
    }

    function loadPosTypeSplitAnswerData(mixed $answer): array {
        [$beginPos, $endPos] = $this->getPoseSpilt();
        return CustomFormLoadHelper::loadSplit($answer, $beginPos, $endPos);
    }


}
