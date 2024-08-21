<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Filament\Forms\Components\Component;

class HideEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasFormTargets;

    public static function identifier(): string {
        return "hidden_event";
    }



    public function handleAfterRenderForm(bool $triggered, array $arguments, Component $component, RuleEvent $rule): Component
    {
        if(empty($rule->data)) return $component;
        if(empty($rule->data["targets"])) return $component;

        $customFieldId = $this->getCustomField($arguments)->identifier;
        if($triggered && in_array($customFieldId, $rule->data["targets"])) return $component->hidden();
        else return $component;
    }

    public function handleAfterRenderInfolist(bool $triggered, array $arguments, \Filament\Infolists\Components\Component $component, RuleEvent $rule): \Filament\Infolists\Components\Component
    {
        if(empty($rule->data)) return $component;
        if(empty($rule->data["targets"])) return $component;

        $customFieldId = $this->getCustomField($arguments)->toArray()["identifier"];

        if($triggered && in_array($customFieldId, $rule->data["targets"])) return $component->hidden();
        else return $component;
    }


    public function getFormSchema(): array
    {
        return [
            $this->getTargetsSelect()
        ];
    }
}
