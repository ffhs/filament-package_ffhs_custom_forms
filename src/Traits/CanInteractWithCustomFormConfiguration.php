<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FormConfigurationNotDefinedException;

trait CanInteractWithCustomFormConfiguration
{
    public function getFormConfiguration(string $customFormIdentifier): CustomFormConfiguration
    {
        $formConfiguration = $this->getFormConfigurations()[$customFormIdentifier] ?? null;

        if (is_null($formConfiguration)) {
            throw new FormConfigurationNotDefinedException(
                'For ' . $customFormIdentifier . ' is no CustomFormConfiguration defined.'
            );
        }

        return $formConfiguration;
    }

    public function getFormConfigurations(): array
    {
        return once(fn() => collect(array_keys($this->config('form_configurations', [])))
            ->mapWithKeys(fn(string|CustomFormConfiguration $formConfig
            ) => [$formConfig::identifier() => $formConfig::make()])
            ->toArray());
    }
}
