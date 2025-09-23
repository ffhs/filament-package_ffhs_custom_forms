<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;


use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;

trait HasFormIdentifier
{
    public function dynamicFormConfiguration(): CustomFormConfiguration
    {
        return CustomForms::getFormConfiguration($this->custom_form_identifier);
    }
}
