<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

trait HasFieldRuleType{

    protected function getFormModel(array $arguments): CustomForm
    {
        $record = $arguments['form'];
        if($record instanceof CustomFormAnswer) return $record->customForm;
        return $record;
    }

    protected function getFieldModel(array $arguments): CustomField
    {
        return $arguments['field'];
    }

    protected function getFormState(array $arguments): array
    {
        return $arguments['state'];
    }

}
