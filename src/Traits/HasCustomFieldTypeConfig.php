<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;

trait HasCustomFieldTypeConfig
{
    public function getConfigAttribute(string $attribute, ?CustomFormConfiguration $formConfiguration = null): mixed
    {
        $path = implode('.', ['type_settings', $this::identifier(), $attribute]);
        if (is_null($formConfiguration)) {
            CustomForms::config('default_form_configuration.' . $path);
        }
        return $formConfiguration::config($path, null);
    }
}
