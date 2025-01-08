<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter;

use Error;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FormImportException;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\Traids\ImportCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\Traids\ImportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\Traids\ImportRuleInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Facades\DB;

class FormSchemaImporter
{
    use ImportFieldInformation;
    use ImportCustomForm;
    use ImportRuleInformation;

    public static function make(): static
    {
        return app(static::class);
    }

    public function import(array $rawForm, DynamicFormConfiguration $configuration, array $formInformation = [], array $templateMap = [], array $generalFieldMap = []): CustomForm{

        DB::beginTransaction();

        try {
            $fieldInformations = $rawForm['fields'] ?? [];
            $ruleInformations = $rawForm['rules'] ?? [];
            unset($rawForm['fields']);
            unset($rawForm['rules']);


            $customForm = $this->importCustomForm($rawForm, $formInformation, $configuration);

            $this->importFields($fieldInformations, $customForm, $templateMap, $generalFieldMap);

            $this->importRule($ruleInformations, $customForm);

            DB::commit();
            return $customForm;
        }catch (Error|\Exception $exception){
            DB::rollBack();
            throw new FormImportException($exception->getMessage());
        }
    }


}
