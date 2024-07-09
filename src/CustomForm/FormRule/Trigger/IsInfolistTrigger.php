<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule\FieldRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfoComponent;

class IsInfolistTrigger extends FieldRuleTriggerType
{
    use HasRuleTriggerPluginTranslate;

    public static function identifier(): string {
        return "infolist_view";
    }

    public function getCreateAnchorData(): array {
        return [];
    }



    public function triggerOnForm(array $arguments, Component $component, FieldRule $rule): bool
    {
        return false;
    }

    public function triggerOnInfolist(array $arguments, InfoComponent $component, FieldRule $rule): bool
    {
        return true;
    }



    public function ruleEditSchema(): array
    {
        return [];
    }

}
