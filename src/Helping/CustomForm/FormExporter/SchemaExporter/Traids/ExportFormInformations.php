<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

class ExportFormInformations
{

    protected function exportFormInformations(CustomForm $form): array
    {
        $formInformations = [
          'tempalte_identifier' => $form
        ];

        CustomForm::class



    }

}
