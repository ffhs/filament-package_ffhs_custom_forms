<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter;

use Error;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FormImportException;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter\Traids\ImportCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter\Traids\ImportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter\Traids\ImportRuleInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasStaticMake;
use Illuminate\Support\Facades\DB;

class FormSchemaImporter
{
    use ImportFieldInformation;
    use ImportCustomForm;
    use ImportRuleInformation;
    use HasStaticMake;

    public function import(
        array $rawForm,
        CustomFormConfiguration $configuration,
        array $formInformation = [],
        array $templateMap = [],
        array $generalFieldMap = []
    ): CustomForm {

        //ToDo Check if the identifiers of the fields exist

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
        } catch (Error|\Exception $exception) {
            DB::rollBack();
            throw new FormImportException($exception);
        }
    }


    public function importWithExistingForm(
        array $rawForm,
        CustomForm $customForm,
        array $templateMap = [],
        array $generalFieldMap = []
    ): CustomForm {

        //ToDo Check if the identifiers of the fields exist

        DB::beginTransaction();

        try {
            $fieldInformations = $rawForm['fields'] ?? [];
            $ruleInformations = $rawForm['rules'] ?? [];
            unset($rawForm['fields']);
            unset($rawForm['rules']);


            $this->importFields($fieldInformations, $customForm, $templateMap, $generalFieldMap);

            $this->importRule($ruleInformations, $customForm);

            DB::commit();
            return $customForm;
        } catch (Error|\Exception $exception) {
            DB::rollBack();
            throw new FormImportException($exception);
        }
    }

}
