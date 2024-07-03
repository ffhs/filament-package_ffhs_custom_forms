<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule;

trait HasRulePluginTranslate
{
    public function getDisplayName():string {
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.rules." . $this->identifier());
    }
}
