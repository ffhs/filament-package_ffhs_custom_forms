<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SplittedType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;

abstract class CustomSplitType extends CustomLayoutType
{
    public function hasSplitFields(): bool
    {
        return true;
    }



}
