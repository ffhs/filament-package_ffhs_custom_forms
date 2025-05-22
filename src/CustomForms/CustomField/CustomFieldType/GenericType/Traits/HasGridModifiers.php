<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits;

trait HasGridModifiers
{
    public function isFullSizeField(): bool
    {
        return false;
    }

    public function getColumns($itemState): int|null
    {
        $columns = $itemState['options']['columns'] ?? null;
        if (!empty($columns)) {
            return $columns;
        }
        return $this->getStaticColumns();
    }

    public function getStaticColumns(): int|null
    {
        return null;
    }
}
