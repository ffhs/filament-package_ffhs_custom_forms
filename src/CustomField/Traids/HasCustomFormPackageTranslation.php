<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids;

trait HasCustomFormPackageTranslation
{
    public function getTranslatedName():string{
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.types." . self::fieldIdentifier());
    }
}
