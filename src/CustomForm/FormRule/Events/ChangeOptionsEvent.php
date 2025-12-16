<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleEvent;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Select;
use Filament\Support\Components\Component;
use Illuminate\Support\Collection;
use ReflectionClass;

class ChangeOptionsEvent extends OptionsEvent
{
    protected static string $identifier = 'change_options_rule';

    public function getConfigurationSchema(): array
    {
        return [
            $this->getTargetSelect(),
            Select::make('selected_options')
                ->label('Anzuzeigende Optionen')
                ->multiple()
                ->hidden(function ($set, $get) {
                    //Fields with an array doesn't generate properly
                    if (is_null($get('selected_options'))) {
                        $set('selected_options', []);
                    }
                })
                ->options($this->getCustomOptionsOptions(...))
        ];
    }

    public function handleAfterRenderFormComponent(
        EmbedRuleEvent $rule,
        mixed $target,
        array $arguments = []
    ): Component {
        $identifier = $arguments['identifier'];

        if ($identifier !== ($rule->data['target'] ?? '')) {
            return $target;
        }
        if (!in_array(HasOptions::class, class_uses_recursive($target::class),
            true)) {
            return $target;
        }

        /** @var Component $target */
        $reflection = new ReflectionClass($target);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);
        $optionsOld = $property->getValue($target);

        /** @phpstan-ignore-next-line */
        $target->options($this->getModifiedOptionsClosure($rule, $target, $optionsOld, $arguments, $identifier));

        return $target;
    }

    public function getOldProperty(Component $target, string $property): mixed
    {
        $reflection = new ReflectionClass($target);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($target);
    }

    protected function getModifiedOptionsClosure(
        EmbedRuleEvent $rule,
        Component $target,
        mixed $optionsOld,
        array $arguments,
        mixed $identifier
    ): Closure {
        return static function ($get, $set) use ($arguments, $identifier, $optionsOld, $target, $rule) {
            $triggered = $rule->getRule()->getTriggersCallback($target, $arguments)();
            /**@var array|Collection $options */
            $options = $target->evaluate($optionsOld);
            $options = is_array($options) ? collect($options) : $options;

            if (!$triggered) {
                return $options;
            }

            $selectedOptions = $rule->data['selected_options'] ?? [];
            $options = $options
                ->filter(fn($option, $key) => in_array($key, $selectedOptions, true))
                ->toArray();

            //Check to replace the current value
            $currentValue = $get($identifier);

            if (is_array($currentValue)) {
                //If Multiple
                $diff = array_intersect($currentValue, array_keys($options));

                if (count($diff) !== count($currentValue)) {
                    $set($identifier, $diff);
                }
            } elseif (!array_key_exists($currentValue, $options)) {
                $set($identifier, null);
            }

            return $options;
        };
    }
}
