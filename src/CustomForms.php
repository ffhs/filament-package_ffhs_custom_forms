<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanInteractionWithFieldTypes;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanInteractWithCustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCachedForms;

class CustomForms
{
    use CanInteractionWithFieldTypes;
    use CanInteractWithCustomFormConfiguration;
    use HasCachedForms;

    public function getFormRuleTriggerClasses(): array
    {
        return once(function (): array {
            $classes = $this->config('default_form_configuration.rule.trigger', []);

            foreach ($this->config('form_configurations', []) as $formConfig) {
                $extra = $formConfig['rule']['trigger'] ?? [];
                $classes = array_merge($classes, $extra);
            }

            return $classes;
        });
    }

    public function getFormRuleEventClasses(): array
    {
        return once(function (): array {
            $classes = $this->config('default_form_configuration.rule.event', []);

            foreach ($this->config('form_configurations', []) as $formConfig) {
                $extra = $formConfig['rule']['event'] ?? [];
                $classes = array_merge($classes, $extra);
            }

            return $classes;
        });
    }


    public function config($key, mixed $default = null)
    {
        return config("ffhs_custom_forms.{$key}", $default);
    }
}
