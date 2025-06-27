<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption;

trait TypeOptionPluginTranslate
{
    protected function translate($key)
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.fields.type_options.' . $key);
    }
}
