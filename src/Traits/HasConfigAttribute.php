<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

trait HasConfigAttribute
{
    public function getConfigAttribute(string $attribute): mixed
    {
        return config('ffhs_custom_forms.type_settings.' . $this::identifier() . '.' . $attribute);
    }
}
