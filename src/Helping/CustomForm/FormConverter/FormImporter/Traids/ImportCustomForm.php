<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait ImportCustomForm
{

    public function importCustomForm(array $rawForm, array $formInformation, DynamicFormConfiguration $configuration)
    {
        $formInformation = array_merge( $rawForm ?? [], $formInformation);
        $formInformation['custom_form_identifier'] = $configuration::identifier();
        return CustomForm::create($formInformation);
    }

}
