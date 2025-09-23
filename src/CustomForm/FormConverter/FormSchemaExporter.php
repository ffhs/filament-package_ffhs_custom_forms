<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
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

    public function export(EmbedCustomForm $form): array
    {
        return [
            'form' => $this->exportFormInformation($form),
            'fields' => $this->exportFieldInformation($form->getOwnedFields()),
            'rules' => $this->exportRuleInformation($form->getOwnedRules()),
        ];
    }
}
