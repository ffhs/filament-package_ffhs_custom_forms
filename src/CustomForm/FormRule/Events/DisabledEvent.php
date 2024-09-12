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
    protected $property ="isDisabled";
    public static function identifier(): string {
        return "disabled_event";
    }
    protected function property(): string
    {
        return $this->property;
    }
    protected function dominatingSide(): bool
    {
        return false;
    }

    public function handleAfterRenderInfolist(Closure $triggers, array $arguments, InfolistComponent  &$component, RuleEvent $rule): InfolistComponent
    {
        $this->property = "isHidden";
        return parent::handleAfterRenderInfolist($triggers, $arguments, $component, $rule);
    }

}
