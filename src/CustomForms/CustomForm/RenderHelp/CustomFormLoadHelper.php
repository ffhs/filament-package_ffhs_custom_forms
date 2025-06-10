<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp;

class CustomFormLoadHelper
{
    public static function load(
        CustomFormAnswer $answerer,
        int|null $begin = null,
        int|null $end = null,
        ?CustomForm $customForm = null
    ): array {
        //ToDo check to Cache stuff for performance $customFields = $answerer->customForm->customFields;
        if (is_null($customForm)) {
            $customForm = $answerer->customForm;
        }

        $loadedData = [];
        $customFields = $answerer->customForm->customFields->keyBy('id');
        $templateFields = $customForm->ownedFields;
        $formRules = $customForm->rules;

        /**@var CustomFieldAnswer $fieldAnswer */
        foreach ($answerer->customFieldAnswers as $fieldAnswer) {
            /**@var CustomField $customField */
            $customField = $customFields->get($fieldAnswer->custom_field_id);

            if (is_null($customField)
                || static::passField($customField, $customForm, $templateFields, $begin, $end)) {
                continue;
            }

            $fieldData = $customField
                ->getType()
                ?->prepareLoadFieldData($fieldAnswer, $fieldAnswer->answer);
            $fieldData = static::runRulesForFieldData($answerer, $fieldData, $formRules); //10ms

            $dataIdentifier = $customField->identifier;
            $dataIdentifier .= empty($fieldAnswer->path) ? '' : '.' . $fieldAnswer->path;

            $loadedData[$dataIdentifier] = $fieldData;
        }

        $fields = $customFields
            ->sortBy('form_position')
            ->keyBy(fn(CustomField $item) => $item->identifier);

        foreach ($loadedData as $key => $data) {
            if (!str_contains($key, '.')) {
                continue;
            }

            $keyPath = explode('.', $key);
            $identifier = $keyPath[0];

            $path = implode('.', array_slice($keyPath, 1));
            $pathResolved = static::findPath($fields, $fields->get($identifier), $path) . '.' . $identifier;
            Arr::set($loadedData, $pathResolved, $data);
            unset($loadedData[$key]);
        }

        return $loadedData;
    }

    public static function runRulesForFieldData(CustomFormAnswer $answerer, mixed $fieldData, $formRules): mixed
    {
        foreach ($formRules as $rule) {
            /**@var Rule $rule */
            $fieldData = $rule->handle(['action' => 'load_answerer', 'custom_field_answerer' => $answerer], $fieldData);
        }

        return $fieldData;
    }

    protected static function findPath(Collection $fields, CustomField $childField, string $path): string
    {
        /**@var CustomField $field */
        $nearestParent = null;

        foreach ($fields as $field) {
            if ($childField->custom_form_id !== $field->custom_form_id) {
                continue;
            }
            if ($field->form_position >= $childField->form_position) {
                break;
            }
            if ($field->layout_end_position < $childField->form_position) {
                continue;
            }
            if (!$field->getType()?->hasSplitFields()) {
                continue;
            }
            $nearestParent = $field;
        }

        $pathFragments = explode('.', $path);
        $path = end($pathFragments);
        //Get Last Path element
        $lastPath = sizeof($pathFragments) > 1 ? implode(' ', array_slice($pathFragments, 0, -1)) : null;

        if (is_null($nearestParent)) {
            return '';
        }
        if (!is_null($lastPath)) {
            $resultPathParent = static::findPath($fields, $nearestParent, $lastPath);
            return $resultPathParent . '.' . $nearestParent->identifier . '.' . $path;
        }

        return $nearestParent->identifier . '.' . $path;
    }

    private static function passField(
        CustomField $customField,
        CustomForm $customForm,
        Collection $templateFields,
        ?int $begin,
        ?int $end
    ): bool {
        if ($customForm->id !== $customField->custom_form_id) {
            $customField = $templateFields->firstWhere('template_id', $customField->custom_form_id);

            if (!$customField) {
                return true;
            }
        }

        $beginCondition = is_null($begin) || $begin <= $customField->form_position;
        $endCondition = is_null($end) || $customField->form_position <= $end;

        return !($beginCondition && $endCondition);
    }
}
