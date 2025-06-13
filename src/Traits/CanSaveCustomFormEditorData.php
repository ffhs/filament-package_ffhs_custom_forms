<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Facades\DB;
use Throwable;

trait CanSaveCustomFormEditorData
{
    use CanSaveCustomFormEditorFields;
    use CanSaveCustomFormEditorRules;

    public function saveCustomFormEditorData(array $rawState, CustomForm $form): void
    {
        try {
            DB::beginTransaction();

            $this->saveFields($rawState['custom_fields'], $form);
            $this->savingRules($rawState['rules'], $form);

            DB::commit();

        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

    }
}
