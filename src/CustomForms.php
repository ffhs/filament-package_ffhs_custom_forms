<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanInteractionWithFieldTypes;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanInteractWithCustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCachedForms;

class CustomForms
{
    use CanInteractionWithFieldTypes;
    use CanInteractWithCustomFormConfiguration;
    use HasCachedForms;
}
