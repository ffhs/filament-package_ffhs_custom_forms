<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\DataManagment\HasCustomFormSaveDataManagement;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanInteractionWithFieldTypes;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;

class CustomForms
{
    use HasCustomFormSaveDataManagement;
    use CanLoadCustomFormEditorData;

    use CanInteractionWithFieldTypes;
}

