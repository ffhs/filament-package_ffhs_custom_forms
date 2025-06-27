<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;


use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;

trait HasFormIdentifier
{
    public function dynamicFormConfiguration(): CustomFormConfiguration
    {
        $clazz = collect(config('ffhs_custom_forms.forms'))
            ->firstWhere(fn(string $class) => $class::identifier() == $this->custom_form_identifier);

        return new $clazz();
    }
}
