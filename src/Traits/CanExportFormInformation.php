<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait CanExportFormInformation
{
    public function exportFormInformation(CustomForm $form): array
    {
        $formInformation = [
            'short_title' => $form->short_title
        ];

        if (!is_null($form->template_identifier)) {
            $formInformation['template_identifier'] = $form->template_identifier;
        }

        return $formInformation;
    }
}
