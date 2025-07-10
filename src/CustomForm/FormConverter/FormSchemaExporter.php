<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanExportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanExportFormInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanExportRuleInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasStaticMake;

class FormSchemaExporter
{
    use CanExportFormInformation;
    use CanExportFieldInformation;
    use CanExportRuleInformation;
    use HasStaticMake;

    public function export(CustomForm $form): array
    {
        return [
            'form' => $this->exportFormInformation($form),
            'fields' => $this->exportFieldInformation($form->ownedFields),
            'rules' => $this->exportRuleInformation($form->ownedRules),
        ];
    }
}
