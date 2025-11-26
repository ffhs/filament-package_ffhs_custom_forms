<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter;

use Ffhs\FfhsUtils\Traits\HasStaticMake;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanExportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanExportFormInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanExportRuleInformation;

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
