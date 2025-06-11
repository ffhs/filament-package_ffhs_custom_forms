<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter\Traids\ExportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter\Traids\ExportFormInformation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter\Traids\ExportRuleInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasStaticMake;

class FormSchemaExporter
{
    use ExportFormInformation;
    use ExportFieldInformation;
    use ExportRuleInformation;
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
