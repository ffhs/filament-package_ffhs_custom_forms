<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits;

trait HasGridModifiers
{
    public function isFullSizeField(): bool
    {
        return false;
    }

    public function getStaticColumns() :int|null
    {
        return null;
    }
}
