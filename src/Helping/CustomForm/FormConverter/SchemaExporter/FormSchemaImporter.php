<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\Traids\ImportCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\Traids\ImportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

class FormSchemaImporter
{
    use ImportFieldInformation;
    use ImportCustomForm;

    public static function make(): static
    {
        return app(static::class);
    }

    public function import(array $rawForm, DynamicFormConfiguration $configuration, array $formInformation = [], array $templateMap = [], array $generalFieldMap = []): CustomForm{

        $fieldInformations = $rawForm['fields'] ?? [];
        $ruleInformations = $rawForm['rules'] ?? [];
        unset($rawForm['fields']);
        unset($rawForm['rules']);


        $customForm = $this->importCustomForm($rawForm, $formInformation, $configuration);


        $this->importFields($fieldInformations, $customForm, $templateMap, $generalFieldMap);

        return $customForm;
    }


}
