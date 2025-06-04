<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

trait HasViewMode
{
    protected string|Closure $viewMode = 'default';

    public function getViewMode(): string|Closure
    {
        return $this->evaluate($this->viewMode);
    }

    public function viewMode(string|Closure $viewMode = 'default'): static
    {
        $this->viewMode = $viewMode;
        return $this;
    }

    public function autoViewMode(): static
    {
        $this->viewMode = static function (?CustomForm $customForm, ?CustomFormAnswer $customFormAnswer) {
            if (is_null($customFormAnswer) || is_null($customForm)) {
                return 'default';
            }

            if ($customFormAnswer->customFieldAnswers->count() === 0) {
                return $customForm->getFormConfiguration()->displayEditMode();
            }
            return $customForm->getFormConfiguration()->displayCreateMode();
        };
        return $this;
    }
}
