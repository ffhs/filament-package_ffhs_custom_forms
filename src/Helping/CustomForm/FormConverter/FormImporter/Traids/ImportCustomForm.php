<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\FormImporter\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait ImportCustomForm
{

    public function importCustomForm(array $rawForm, array $formInformation, CustomFormConfiguration $configuration)
    {
        $formInformation = array_merge($rawForm['form'] ?? [], $formInformation);
        $formInformation['custom_form_identifier'] = $configuration::identifier();
        return CustomForm::create($formInformation);
    }

}
