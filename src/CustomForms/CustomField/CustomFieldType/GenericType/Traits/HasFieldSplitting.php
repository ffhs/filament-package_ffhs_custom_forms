<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits;


trait HasFieldSplitting
{

    public function hasSplitFields(): bool
    {
        return false;
    }

//    public function getSplitField(CustomField $field, array $customFieldAnswererRawData): array
//    {
//        return $customFieldAnswererRawData;
//    }
//
//    public function getSplitFieldOwnedData(CustomField $field, array $customFieldAnswererRawData): array
//    {
//        return [];
//    }
//
//    public function mergeSplitField(CustomField $field, array $fieldAnswerData, array $splitFieldData): array
//    {
//        return $splitFieldData;
//    }
}
