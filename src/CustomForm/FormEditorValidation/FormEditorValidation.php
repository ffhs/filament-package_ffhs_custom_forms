<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormEditorValidation;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

abstract class FormEditorValidation
{
    public abstract function repeaterValidation(CustomForm $record, Closure $fail, array $value, string $attribute):void ;
}
