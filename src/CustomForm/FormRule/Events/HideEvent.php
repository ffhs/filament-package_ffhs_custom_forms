<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule\FieldRuleEventType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Rule;
use Filament\Forms\Components\Component;

class HideEvent extends FieldRuleEventType
{
    use HasRuleEventPluginTranslate;

    public static function identifier(): string {
        return "hidden_event";
    }



    public function handleAfterRenderForm(bool $triggered, array $arguments, Component $component, Rule $rule): Component
    {
        if($triggered) return $component->hidden(true);
        else return $component;
    }

    public function handleAfterRenderInfolist(bool $triggered, array $arguments, \Filament\Infolists\Components\Component $component, Rule $rule): \Filament\Infolists\Components\Component
    {
        if($triggered) return $component->hidden(true);
        else return $component;
    }


    public function getFormSchema(): array
    {
        return [];
    }
}
