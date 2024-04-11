<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

trait UseFieldSplit
{

    protected bool|Closure $useFieldSplit = false;
    protected null|CustomField $fieldSplit = null;

    public function useFieldSplit(bool|Closure $useFieldSplit=true):static {
        $this->useFieldSplit = $useFieldSplit;
        return $this;
    }

    public function fieldSplit(CustomField|Closure|null $layoutTypeSplit):static {
        $this->fieldSplit = $layoutTypeSplit;
        return $this;
    }

    public function isUseFieldSplit(): bool{
        return $this->evaluate($this->useFieldSplit);
    }

    public function getFieldSplit(): ?CustomField{
        return $this->evaluate($this->fieldSplit);
    }

}
