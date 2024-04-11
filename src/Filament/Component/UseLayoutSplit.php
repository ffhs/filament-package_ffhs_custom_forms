<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;

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
}
