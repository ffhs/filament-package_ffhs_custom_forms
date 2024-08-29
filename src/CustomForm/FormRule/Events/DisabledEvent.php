<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Filament\Forms\Components\Component;
use ReflectionClass;
use Filament\Infolists\Components\Component as InfolistComponent;

class DisabledEvent extends IsPropertyOverwriteEvent
{
    public static function identifier(): string {
        return "disabled_event";
    }
    protected function property(): string
    {
        return "isDisabled";
    }
    protected function dominatingSide(): bool
    {
        return false;
    }
}
