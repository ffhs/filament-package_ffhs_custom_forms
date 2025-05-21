<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;

trait HasFormIdentifier
{

    public function dynamicFormConfiguration(): DynamicFormConfiguration {
        $clazz = collect(config("ffhs_custom_forms.forms"))
            ->where(fn(string $class)=> $class::identifier() == $this->custom_form_identifier)
            ->first();
        return new $clazz();
    }

}
