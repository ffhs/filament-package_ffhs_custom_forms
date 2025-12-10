<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;

trait HasRuleEventPluginTranslate
{
    public static function __(string $translate): string
    {
        return FormRule::type__('events.' . static::identifier() . '.' . $translate);
    }

    public static function displayname(): string
    {
        return static::__('label');
    }
}
