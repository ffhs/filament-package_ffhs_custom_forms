<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FormBuilderUtil
{
    public static function buildSchema(?CustomForm $customForm, array $layout): CustomForm
    {
        if (is_null($customForm)) {
            $customForm = CustomForm::create();
        }
        $customField = [];

        static::generateFields($customForm, $layout, $customField);


        foreach ($customField as $data) {
            $customOptions = $data['customOptions'] ?? [];

            $names = $data['name'] ?? [];

            unset($data['name']);
            unset($data['customOptions']);


            $field = new CustomField();
            $field->fill($data);

            foreach ($names as $local => $name) {
                $field->setTranslation('name', $local, $name);
            }

            $field->save();

            if ($field->isGeneralField() && !empty($customOptions)) {
                $field->customOptions()->sync($customOptions);
            } else {
                if (!empty($customOptions)) {
                    $field->customOptions()->createMany($customOptions);
                }
            }
        }

        return $customForm;
    }


    public static function generateFields(CustomForm $form, array $fields, array &$toCreateFields): void
    {
        foreach ($fields as $field) {

            $formPosition = sizeof($toCreateFields) + 1;
            $generalFieldsIdentifier = $form->getFormConfiguration()
                ->getAvailableGeneralFields()
                ->keyBy('identifier');
            $generalFields = $form->getFormConfiguration()
                ->getAvailableGeneralFields();
            $fieldData = [
                'form_position' => $formPosition,
                'custom_form_id' => $form->id,
                'is_active' => $field['is_active'] ?? true,
            ];

            $defaultOptions = [];
            if (array_key_exists('general_field', $field)) {
                $genField = $generalFieldsIdentifier->get($field['general_field']);
                $fieldData['general_field_id'] = $genField->id;
                $defaultOptions = $genField->getType()->getDefaultTypeOptionValues();

            } elseif (array_key_exists('general_field_id', $field)) {
                $fieldData['general_field_id'] = $field['general_field_id'];
                $genField = $generalFields->get($field['general_field_id']);
                $defaultOptions = $genField->getType()->getDefaultTypeOptionValues();

            } elseif (array_key_exists('template', $field)) {
                $template = $form
                    ->getFormConfiguration()
                    ->getAvailableTemplates()
                    ->firstWhere('short_title', $field['template']);
                $fieldData['template_id'] = $template->id;
                $fieldData['identifier'] = uniqid();

            } elseif (array_key_exists('template_id', $field)) {
                $fieldData['template_id'] = $field['template_id'];
            } else {
                //Names
                $fieldData['name'] = [];
                foreach ($field as $key => $value) {
                    if (str_contains($key, 'name_')) {
                        $fieldData['name'][str_replace('name_', '', $key)] = $value;
                    }
                }


                $fieldData['type'] = $field['type'];
                $fieldData['identifier'] = $field['identifier'] ?? uniqid();
                $defaultOptions = CustomFieldType::getTypeFromIdentifier($field['type'])->getDefaultTypeOptionValues();
            }


            //Field Type Options
            self::prepareOptionData($field, $fieldData, $defaultOptions);

            //CustomOptions
            self::prepareCustomOptions($field, $fieldData, $form);

            //Layout Fields
            self::setupLayoutFields($form, $field, $fieldData, $toCreateFields);

            $toCreateFields[] = $fieldData;

        }
    }

    public static function buildRules(?CustomForm $customForm, array $rules): CustomForm
    {

        $finalRules = [];
        $fields = $customForm->customFields;

        foreach ($rules as $rule) {
            if (!key_exists('is_or_mode', $rule)) {
                $rule['is_or_mode'] = false;
            }

            $triggers = self::prepareTriggers($rule['triggers'], $fields);
            $events = self::prepareRuleEvents($rule['events'], $fields);

            unset($rule['triggers']);
            unset($rule['events']);

            $rule = new Rule($rule);
            $rule->save();

            //ToDo Save Many at once
            collect($triggers)
                ->merge($events)
                ->map(fn(Model $model) => $model->fill(['rule_id' => $rule->id])->save());

            $finalRules[] = $rule;
        }

        $ruleIds = collect($finalRules)->pluck('id');
        $customForm->ownedRules()->sync($ruleIds);
        return $customForm;
    }

    public static function prepareTriggers(array $rawTriggers, Collection $fields): array
    {
        $triggers = [];
        $order = 1;

        foreach ($rawTriggers ?? [] as $trigger) {
            $trigger['order'] = $order;
            $order++;

            /*
             *  $type = $trigger['type'];
             if ($type instanceof TriggerType) $trigger['type'] = $type::identifier();
             if (array_key_exists('is_inverted', $trigger)) $trigger['type'] = $type::identifier();
             */

            $trigger = self::prepareTarget($trigger, $fields);
            $triggers[] = new RuleTrigger($trigger);
        }
        return $triggers;
    }


    //ToDo Name Converter

    public static function prepareTarget(mixed $hasData, Collection $fields): mixed
    {
        if (!array_key_exists('data', $hasData)) {
            $hasData['data'] = [];
        } else {
            $data = $hasData['data'];
            if (array_key_exists('target', $data)) {
                $target = $data['target'];
                $found = $fields->firstWhere(fn(CustomField $field) => $field->identifier == $target);
                if (is_null($found)) {
                    $found = $fields->firstWhere(fn(CustomField $field) => $field->name == $target);
                    if (!is_null($found)) {
                        $data['target'] = $found->identifier;
                    }
                }
            }

            if (array_key_exists('targets', $data)) {
                $targets = [];
                foreach ($data['targets'] ?? [] as $target) {
                    $found = $fields->firstWhere(fn(CustomField $field) => $field->identifier == $target);
                    if (!is_null($found)) {
                        $targets[] = $target;
                    } else {
                        $found = $fields->firstWhere(fn(CustomField $field) => $field->name == $target);
                        if (!is_null($found)) {
                            $targets[] = $found->identifier;
                        } else {
                            $targets[] = $target;
                        }
                    }
                }
                $data['targets'] = $targets;
            }

            $hasData['data'] = $data;
        }
        return $hasData;
    }

    public static function prepareRuleEvents(array $rawEvents, Collection $fields): array
    {
        $order = 1;
        foreach ($rawEvents ?? [] as $event) {
            $type = $event['type'];
            if ($type instanceof EventType) {
                $event['type'] = $type::identifier();
            }
            if (!array_key_exists('data', $event)) {
                $event['data'] = [];
            }
            $event['order'] = $order;
            $event = self::prepareTarget($event, $fields);

            $events[] = new RuleEvent($event);
            $order++;
        }
        return $events;
    }

    private static function prepareOptionData(array $field, array &$fieldData, array $defaultOptions): void
    {
        $options = $field['options'] ?? [];
        $fieldData['options'] = [
            ...$defaultOptions,
            ...$options
        ];
    }

    private static function prepareCustomOptions(array $field, array &$fieldData, CustomForm $form): void
    {
        if (!array_key_exists('customOptions', $field)) {
            return;
        }

        $generalFields = $form->getFormConfiguration()
            ->getAvailableGeneralFields();

        //General Field
        if (array_key_exists('general_field_id', $fieldData)) {
            $generalField = $generalFields->get($fieldData['general_field_id']);;
            $allOptions = $generalField->customOptions;

            $fieldData['customOptions'] = [];

            foreach ($field['customOptions'] as $customOption) {
                $optionId = $allOptions->where('id', $customOption)->first()?->id;
                if (is_null($optionId)) {
                    $optionId = $allOptions->where('identifier', $customOption)->first()?->id;
                }
                if (is_null($optionId)) {
                    $optionId = $allOptions->where('name', $customOption)->first()?->id;
                }
                if (!is_null($optionId)) {
                    $fieldData['customOptions'][] = $optionId;
                }
            }
            return;
        }

        $fieldData['customOptions'] = [];

        foreach ($field['customOptions'] as $customOption) {

            $names = [];
            foreach ($customOption as $key => $value) {
                if (str_contains($key, 'name_')) {
                    $names[str_replace('name_', '', $key)] = $value;
                }
            }

            $fieldData['customOptions'][] = [
                'name' => $names,
                'identifier' => $customOption['identifier'] ?? uniqid(),
            ];
        }
    }

    private static function setupLayoutFields(
        CustomForm $form,
        array $field,
        array &$fieldData,
        array &$toCreateFields
    ): void {
        if (!array_key_exists('fields', $field)) {
            return;
        }
        $placeHolderId = uniqid();

        $toCreateFields['placeHolder-' . $placeHolderId] = $fieldData;
        static::generateFields($form, $field['fields'], $toCreateFields);
        $formEndPosition = sizeof($toCreateFields);

        unset($toCreateFields['placeHolder-' . $placeHolderId]);
        $fieldData['layout_end_position'] = $formEndPosition;
    }
}
