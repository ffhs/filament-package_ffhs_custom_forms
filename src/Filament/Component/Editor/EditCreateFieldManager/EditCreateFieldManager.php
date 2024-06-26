<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;

abstract class EditCreateFieldManager
{
    public static abstract function getFieldData(EditCustomFormFields $editField, array $status, array $arguments, string $key): array;

}
