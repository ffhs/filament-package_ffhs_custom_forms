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

    public function config($key, mixed $default = null)
    {
        return config("ffhs_custom_forms.{$key}", $default);
    }
}
