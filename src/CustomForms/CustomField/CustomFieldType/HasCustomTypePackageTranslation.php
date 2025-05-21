<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType;

trait HasCustomTypePackageTranslation
{
    public function getTranslatedName():String{
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.types." . static::identifier());
    }
}
