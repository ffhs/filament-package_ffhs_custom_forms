<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;



use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;


abstract class CustomLayoutType extends CustomFieldType
{
    public function canBeRequired(): bool {
        return false;
    }


}
