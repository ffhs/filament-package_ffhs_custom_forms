<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormRender\EmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\InfolistRender\EmbeddedAnswerInfolist;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Database\Eloquent\Model;

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
