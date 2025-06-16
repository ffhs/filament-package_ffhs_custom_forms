<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait CanImportCustomForm
{
    public function importCustomForm(
        array $rawForm,
        array $formInformation,
        CustomFormConfiguration $configuration
    ): CustomForm {
        $formInformation = array_merge($rawForm['form'] ?? [], $formInformation);
        $formInformation['custom_form_identifier'] = $configuration::identifier();
        return CustomForm::create($formInformation);
    }
}
