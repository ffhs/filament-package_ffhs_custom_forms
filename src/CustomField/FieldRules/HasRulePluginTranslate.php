<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules;

trait HasRulePluginTranslate
{
    public function getTranslatedName():string {
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.rules." . $this->identifier());
    }
}
