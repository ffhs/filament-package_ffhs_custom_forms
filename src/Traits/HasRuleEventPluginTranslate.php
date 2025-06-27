<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;

trait HasRuleEventPluginTranslate
{
    public static function __(string $translate)
    {
        return FormRule::type__('events.' . static::identifier() . '.' . $translate);
    }

    public function getDisplayName(): string
    {
        return static::__('label');
    }
}
