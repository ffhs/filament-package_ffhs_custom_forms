<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter\Traids\ExportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter\Traids\ExportFormInformation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\SchemaExporter\Traids\ExportRuleInformation;
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
}
