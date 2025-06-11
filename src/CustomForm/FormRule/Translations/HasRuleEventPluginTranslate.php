<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations;

trait HasRuleEventPluginTranslate
{
    public function getDisplayName(): string
    {
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.rules.event." . $this->identifier());
    }
}
