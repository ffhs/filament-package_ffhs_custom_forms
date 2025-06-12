<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use ReflectionClass;

class ChangeOptionsEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasTriggerEventFormTargets;
    use CanMapFields;

    public static function identifier(): string
    {
        return 'change_options_rule';
    }

    public function getFormSchema(): array
    {
        return [
            $this->getTargetSelect(),
            Select::make('selected_options')
                ->label('Anzuzeigende Optionen')
                ->multiple()
                ->hidden(function ($set, $get) {
                    //Fields with an array doesn't generate properly
                    if ($get('selected_options') == null) {
                        $set('selected_options', []);
                    }
                })
                ->options($this->getCustomOptionsOptions(...))
        ];
    }

    public function handleAfterRenderForm(
        Closure $triggers,
        array $arguments,
        Component &$component,
        RuleEvent $rule
    ): Component {
        if ($arguments['identifier'] !== ($rule->data['target'] ?? '')) {
            return $component;
        }
        $customField = $this->getCustomField($arguments);
        if (!in_array(HasOptions::class, class_uses_recursive($component::class))) {
            return $component;
        }


        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);
        $optionsOld = $property->getValue($component);

        //ToDo Refactor and maybie simplify
        $component->options(function ($get, $set) use ($triggers, $optionsOld, $customField, $component, $rule) {
            $triggered = $triggers(['state' => $get('.')]);
            $options = $component->evaluate($optionsOld);
            if (!$triggered) {
                return $options;
            }
            if ($options instanceof Collection) {
                $options = $options->toArray();
            }
            foreach ($options as $key => $option) {
                if (in_array($key, $rule->data['selected_options'])) {
                    continue;
                }
                unset($options[$key]);
            }

            //Check to replace the current value
            $currentValue = $get($this->getIdentifyKey($customField));
            if (is_array($currentValue)) {
                if (!is_array(($currentValue[0] ?? []))) {
                    $diff = array_intersect($currentValue, array_keys($options));
                    if (sizeof($diff) != sizeof($currentValue)) {
                        $set($customField->identifier, $diff);
                    }
                }
            } else {
                if (!array_key_exists($currentValue, $options)) {
                    $set($customField->identifier, null);
                }
            }

            return $options;
        });

        return $component;
    }


    public function getCustomOptionsOptions($get, CustomForm $record)
    {
        $field = $this->getTargetFieldData($get);

        if (empty($field)) {
            return [];
        }


        if (!empty($field['general_field_id'])) {
            $customField = new CustomField();
            $customField->fill($field);
            $genOptions = $customField->generalField->customOptions;
            $selectedOptions = $this->getTargetFieldData($get)['options']['customOptions'] ?? [];
            $genOptions = $genOptions->whereIn('id', $selectedOptions);
            return $genOptions->pluck('name', 'identifier');
        }

        if (!array_key_exists('options', $field)) {
            $field['options'] = [];
        }
        if (!array_key_exists('customOptions', $field['options'])) {
            $field['options']['customOptions'] = [];
        }
        $options = $field['options']['customOptions'];

        return collect($options)->pluck('name.' . $record->getLocale(), 'identifier');
    }


    protected function getTargetOptions($get, $record): array
    {
        $output = [];
        collect($this->getAllFieldsData($get))
            ->map(fn($field) => (new CustomField())->fill($field))
            ->filter(fn(CustomField $field) => $field->getType() instanceof CustomOptionType)
            ->each(function (CustomField $field) use ($record, &$output) {
                $title = $field->customForm?->short_title;
                if (empty($title)) {
                    $title = $record?->short_title;
                }
                if (empty($title)) {
                    $title = '?';
                }

                $output[$title][$field->identifier] = $field->name ?? ' ';
            });

        return $output;
    }


}
