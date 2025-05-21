<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp;

use Closure;

trait UseViewMode
{
    protected string|Closure $viewMode;

    public function getViewMode(): string|Closure {
        return $this->evaluate($this->viewMode);
    }
    public function viewMode(string|Closure $viewMode = "default"): static {
        $this->viewMode = $viewMode;
        return $this;
    }
}
