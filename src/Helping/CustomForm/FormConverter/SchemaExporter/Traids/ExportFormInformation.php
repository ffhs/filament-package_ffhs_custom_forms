<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait ExportFormInformation
{

    public function exportFormInformation(CustomForm $form): array
    {

        $formInformation = [
            'short_title' => $form->short_title
        ];

        if(!is_null($form->template_identifier)){
            $formInformation['template_identifier'] = $form->template_identifier;
        }

        return $formInformation;
    }

}
