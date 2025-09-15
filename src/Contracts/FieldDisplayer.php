<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;


use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;

interface FieldDisplayer
{
    public function __invoke(string $viewMode, EmbedCustomField $customField, array $parameter);
}
