<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

trait UsePosSplit
{

    protected bool|Closure $usePoseSplit = false;
    protected array|Closure|null $poseSplit = null;


    //The array have to look like: [$beginPos,$endPos]
    public function usePoseSplit(bool|Closure $useFieldSplit):static {
        $this->usePoseSplit = $useFieldSplit;
        return $this;
    }

    public function poseSplit(array|Closure|null $layoutTypeSplit):static {
        $this->poseSplit = $layoutTypeSplit;
        return $this;
    }

    public function isUsePoseSplit(): bool{
        return $this->evaluate($this->usePoseSplit);
    }

    public function getPoseSpilt(): ?array{
        return $this->evaluate($this->poseSplit);
    }


}
