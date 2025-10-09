<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Exception;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;

trait HasFormConfiguration
{
    protected string|\Closure|CustomFormConfiguration $formConfiguration;

    public function getFormConfiguration(): CustomFormConfiguration
    {
        if (!method_exists($this, 'evaluate')) {
            throw new Exception('Method evaluate not found');
        }

        $formConfiguration = $this->evaluate($this->formConfiguration);
        if ($formConfiguration instanceof CustomFormConfiguration) {
            return $formConfiguration;
        }
        $this->formConfiguration = $formConfiguration::make();
        return $this->formConfiguration;
    }

    public function formConfiguration(string|\Closure|CustomFormConfiguration $formConfiguration): static
    {
        $this->formConfiguration = $formConfiguration;
        return $this;
    }

}
