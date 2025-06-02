<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FormConfigurationNotDefinedException;

trait CanInteractWithCustomFormConfiguration
{
    public function getFormConfiguration(string $customFormIdentifier): CustomFormConfiguration
    {
        $formConfiguration = $this->getFormConfigurations()[$customFormIdentifier] ?? null;
        if (is_null($formConfiguration)) {
            throw new FormConfigurationNotDefinedException('For ' . $customFormIdentifier . ' is no CustomFormConfiguration defined.');
        }

        return $formConfiguration;
    }

    private function getFormConfigurations(): array
    {
        return once(function () {
            return collect(config("ffhs_custom_forms.forms"))->mapWithKeys(function ($formConfig) {
                return [$formConfig::identifier() => $formConfig::make()];
            })->toArray();
        });
    }

}
