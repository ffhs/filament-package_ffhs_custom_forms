<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits;


use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

trait HasFieldSplitting
{

    public function hasSplitFields(): bool
    {
        return false;
    }

    public function getSplitField(CustomField $field, array $fieldAnswerData): array
    {
        return $fieldAnswerData;
    }

    public function mergeSplitField(CustomField $field, array $fieldAnswerData, array $splitFieldData): array
    {
        return $splitFieldData;
    }
}
