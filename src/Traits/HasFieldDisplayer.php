<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;

trait HasFieldDisplayer
{
    protected FieldDisplayer|\Closure $fieldDisplayer;

    public function fieldDisplayer(FieldDisplayer|\Closure $fieldDisplayer): static
    {
        $this->fieldDisplayer = $fieldDisplayer;
        return $this;
    }

    public function getFieldDisplayer(): FieldDisplayer
    {
        return $this->evaluate($this->fieldDisplayer);
    }
}
