<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\Traids\ExportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\Traids\ExportFormInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\Traids\ExportRuleInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

class FormSchemaExporter
{
    use ExportFormInformation;
    use ExportFieldInformation;
    use ExportRuleInformation;

    public static function make(): static
    {
        return app(static::class);
    }

    public function export(CustomForm $form): array{
        return [
            'form' => $this->exportFormInformation($form),
            'fields' => $this->exportFieldInformation($form->ownedFields),
            'rules' => $this->exportRuleInformation($form->ownedRules),
        ];
    }

    public function import(array $rawForm): CustomForm{
        return new CustomForm();
    }

}
