<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\DataManagment\HasCustomFormSaveDataManagement;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanInteractionWithFieldTypes;

class CustomForms
{
    use HasCustomFormSaveDataManagement;

    use CanInteractionWithFieldTypes;
}

