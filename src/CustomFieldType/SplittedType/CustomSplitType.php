<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;

abstract class CustomSplitType extends CustomLayoutType
{
    public function hasSplitFields(): bool
    {
        return true;
    }
}
