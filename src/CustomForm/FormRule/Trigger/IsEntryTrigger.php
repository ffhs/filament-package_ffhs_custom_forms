<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Collection;

class IsEntryTrigger extends FormRuleTriggerType
{
    use HasRuleTriggerPluginTranslate;
    use HasTriggerEventFormTargets;

    public static function identifier(): string
    {
        return 'infolist_view';
    }

    public function getConfigurationSchema(): array
    {
        return [];
    }

    public function isTrigger(array $arguments, mixed &$target, EmbedRuleTrigger $trigger): bool
    {
        if (is_array($target)) {
            $component = array_values($target)[0] ?? null;
        } elseif ($target instanceof Collection) {
            $component = $target->first();
        }

        if ($component instanceof Component) {
            $component->getContainer()->getOperation() === 'view';
        }

        return false;
    }
}
