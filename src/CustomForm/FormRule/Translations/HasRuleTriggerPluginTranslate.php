<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\FormRule\Translations;

trait HasRuleTriggerPluginTranslate
{
    public function getDisplayName():string {
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.rules.trigger." . $this->identifier());
    }
}
