<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\SplittedType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;

abstract class CustomSplitType extends CustomLayoutType
{
    public function hasSplitFields(): bool
    {
        return true;
    }



}
