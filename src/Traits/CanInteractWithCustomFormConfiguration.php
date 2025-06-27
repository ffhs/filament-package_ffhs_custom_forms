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

    private function getFormConfigurations(): array
    {
        return once(fn() => collect(config('ffhs_custom_forms.forms'))
            ->mapWithKeys(fn($formConfig) => [$formConfig::identifier() => $formConfig::make()])
            ->toArray());
    }
}
