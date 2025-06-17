<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;

trait HasRuleTriggerPluginTranslate
{
    public static function __(string $translate)
    {
        return FormRule::type__('triggers.' . static::identifier() . '.' . $translate);
    }

    public function getDisplayName(): string
    {
        return static::__('label');
    }
}
