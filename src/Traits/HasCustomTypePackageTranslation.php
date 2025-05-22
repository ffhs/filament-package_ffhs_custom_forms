<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;

trait HasCustomTypePackageTranslation
{
    public function getTranslatedName(): string
    {
        return CustomForms::__('custom_field_types.' . $this::identifier() . '.label');
    }
}
