<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

interface FieldDisplayer
{
    public function __invoke(string $viewMode, CustomField $customField, array $parameter);
}
