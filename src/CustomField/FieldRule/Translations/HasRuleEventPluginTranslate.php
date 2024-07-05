<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule\Translations;

trait HasRuleEventPluginTranslate
{
    public function getDisplayName():string {
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.rules.event." . $this->identifier());
    }
}
