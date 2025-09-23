<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Filament\Infolists\Components\Entry;

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
        $this->viewMode = function () {
            if ($this instanceof Entry) {
                return $this->getFormConfiguration()->displayViewMode();
            }

            if ($this->getCustomFormAnswer()->getCustomFieldAnswers()->count() === 0) {
                return $this->getFormConfiguration()->displayEditMode();
            }

            return $this->getFormConfiguration()
                ->displayCreateMode();
        };

        return $this;
    }
}
