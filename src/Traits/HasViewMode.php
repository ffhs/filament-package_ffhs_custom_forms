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
        $this->viewMode = static function (CustomForm|CustomFormAnswer|null $form) {
            if (is_null($form)) {
                return 'default';
            }

            if ($form instanceof CustomFormAnswer && $form->customFieldAnswers->count() === 0) {
                return $form->customForm
                    ->getFormConfiguration()
                    ->displayEditMode();
            }

            return $form
                ->getFormConfiguration()
                ->displayCreateMode();
        };

        return $this;
    }
}
