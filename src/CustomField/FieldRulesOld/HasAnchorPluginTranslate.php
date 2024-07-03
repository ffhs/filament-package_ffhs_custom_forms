<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld;

trait HasAnchorPluginTranslate
{
    public function getTranslatedName():string {
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.anchors." . $this->identifier());
    }
}
