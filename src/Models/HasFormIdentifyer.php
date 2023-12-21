<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

trait HasFormIdentifyer
{

    public function dynamicFormConfiguration(): string {
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $this->custom_form_identifier)->first();
    }

}
