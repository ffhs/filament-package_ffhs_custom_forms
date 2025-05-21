<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits;

trait HasConfigAttribute
{
    public function getConfigAttribute(string $attribute): mixed {
        return config("ffhs_custom_forms.type_settings." . $this::identifier() . "." . $attribute);
    }

}
