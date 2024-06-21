<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Domain;

trait HasCustomFormPackageTranslation
{
    public function getTranslatedName():String{
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.types." . static::identifier());
    }
}
