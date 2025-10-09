<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter;

use Error;
use Exception;
use Ffhs\FfhsUtils\Traits\HasStaticMake;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FormImportException;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanImportCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanImportFieldInformation;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanImportRuleInformation;
use Illuminate\Support\Facades\DB;

class FormSchemaImporter  //ToDo let it work with Embedet forms
{
    use CanImportFieldInformation;
    use CanImportCustomForm;
    use CanImportRuleInformation;
    use HasStaticMake;

    /**
     * @throws FormImportException
     */
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
            unset($rawForm['fields'], $rawForm['rules']);

            $customForm = $this->importCustomForm($rawForm, $formInformation, $configuration);

            $this->importFields($fieldInformations, $customForm, $templateMap, $generalFieldMap);
            $this->importRule($ruleInformations, $customForm);

            DB::commit();

            return $customForm;
        } catch (Error|Exception $exception) {
            DB::rollBack();

            throw new FormImportException($exception);
        }
    }


    /**
     * @throws FormImportException
     */
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
            unset($rawForm['fields'], $rawForm['rules']);

            $this->importFields($fieldInformations, $customForm, $templateMap, $generalFieldMap);
            $this->importRule($ruleInformations, $customForm);

            DB::commit();

            return $customForm;
        } catch (Error|Exception $exception) {
            DB::rollBack();

            throw new FormImportException($exception);
        }
    }
}
