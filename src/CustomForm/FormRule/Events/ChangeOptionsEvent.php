<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleEventPluginTranslate;
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
                    if (is_null($get('selected_options'))) {
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
        $identifier = $arguments['identifier'];
        if ($identifier !== ($rule->data['target'] ?? '')) {
            return $component;
        }
        if (!in_array(HasOptions::class, class_uses_recursive($component::class), true)) {
            return $component;
        }
        /** @var HasOptions|Component $component */
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);
        $optionsOld = $property->getValue($component);

        $component->options($this->getModifiedOptionsClosure($identifier, $triggers, $optionsOld, $component, $rule));

        return $component;
    }

    public function getCustomOptionsOptions($get, CustomForm $record)
    {
        $field = $this->getTargetFieldData($get, $record);

        if (empty($field)) {
            return [];
        }

        if (!empty($field['general_field_id'])) {
            $customField = new CustomField();
            $customField->fill($field);
            $genOptions = $customField->generalField->customOptions; //toDo TempField
            $selectedOptions = $this->getTargetFieldData($get, $record)['options']['customOptions'] ?? [];
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

    /**
     * @param mixed $identifier
     * @param Closure $triggers
     * @param mixed $optionsOld
     * @param Component $component
     * @param RuleEvent $rule
     * @return Closure
     */
    public function getModifiedOptionsClosure(
        mixed $identifier,
        Closure $triggers,
        mixed $optionsOld,
        Component $component,
        RuleEvent $rule
    ): Closure {
        return static function ($get, $set) use ($identifier, $triggers, $optionsOld, $component, $rule) {
            /**@var array|Collection $options */
            $triggered = $triggers(['state' => $get('.')]);
            $options = $component->evaluate($optionsOld);
            $options = is_array($options) ? collect($options) : $options;
            if (!$triggered) {
                return $options;
            }

            /**@var array|Collection $array */
            $options = $options
                ->filter(fn($option, $key) => in_array($key, $rule->data['selected_options'], true))
                ->toArray();

            //Check to replace the current value
            $currentValue = $get($identifier);
            if (is_array($currentValue)) {
                //If Multiple
                $diff = array_intersect($currentValue, array_keys($options));
                if (sizeof($diff) !== sizeof($currentValue)) {
                    $set($identifier, $diff);
                }
            } elseif (!array_key_exists($currentValue, $options)) {
                $set($identifier, null);
            }

            return $options;
        };
    }


    protected function getTargetOptions($get, ?CustomForm $record): array
    {
        if (is_null($record)) {
            return [];
        }

        $output = [];
        collect($this->getAllFieldsData($get, $record))
            ->map(function ($field) use ($record) {
                $customField = new CustomField($field);
                if ($customField->isGeneralField()) {
                    $genField = $record
                        ->getFormConfiguration()
                        ->getAvailableGeneralFields()
                        ->get($customField->general_field_id);

                    $customField->setRelation('generalField', $genField);
                }
                if ($customField->custom_form_id === $record->id) {
                    $customField->setRelation('customForm', $record);
                } else {
                    $template = $record
                        ->getFormConfiguration()
                        ->getAvailableTemplates()
                        ->get($customField->custom_form_id);
                    $customField->setRelation('customForm', $template);
                }

                return $customField;
            })
            ->filter(fn(CustomField $field) => $field->getType() instanceof CustomOptionType)
            ->each(function (CustomField $field) use ($record, &$output) {
                $title = $field->customForm?->short_title;
                if (empty($title)) {
                    $title = '?';
                }

                $output[$title][$field->identifier] = $field->name ?? ' ';
            });

        return $output;
    }
}
