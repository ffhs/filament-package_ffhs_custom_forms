<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;

trait HasRuleTriggerPluginTranslate
{
    public static function __(string $translate): string
    {
        return FormRule::type__('triggers.' . static::identifier() . '.' . $translate);
    }

    public static function displayname(): string
    {
        return static::__('label');
    }
}
