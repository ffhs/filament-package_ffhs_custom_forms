<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait CanLoadFormAnswer
{
    public function modifyFieldDataFormRules(CustomFormAnswer $answerer, mixed $fieldData, $formRules): mixed
    {
        foreach ($formRules as $rule) {
            /**@var Rule $rule */
            $fieldData = $rule->handle(['action' => 'load_answer', 'custom_field_answerer' => $answerer], $fieldData);
        }

        return $fieldData;
    }

    public function loadCustomAnswerData(
        CustomFormAnswer $answerer,
        int|null $begin = null,
        int|null $end = null
    ): array {
        $loadedData = [];
        $customForm = $answerer->customForm;
        $customFields = $customForm->customFields->keyBy('id');
        $templateTypeFields = $customFields->whereNotNull('template_id')->keyBy('template_id');
        $formRules = $customForm->rules;

        /**@var CustomFieldAnswer $fieldAnswer */
        foreach ($answerer->customFieldAnswers as $fieldAnswer) {
            /**@var CustomField $customField */
            $customField = $customFields->get($fieldAnswer->custom_field_id);
            $isFieldInRange = $this->isFieldInRange($customField, $customForm, $templateTypeFields, $begin, $end);

            if (!$isFieldInRange) {
                continue;
            }

            $fieldData = $customField->getType()?->prepareLoadAnswerData($fieldAnswer, $fieldAnswer->answer);
            $fieldData = $this->modifyFieldDataFormRules($answerer, $fieldData, $formRules); //10ms
            $dataIdentifier = $this->getDataIdentifier($fieldAnswer, $customField);

            $loadedData[$dataIdentifier] = $fieldData;
        }

        $customFields = $customFields
            ->sortBy('form_position')
            ->keyBy(fn(CustomField $item) => $item->identifier);


        $loadedData = $this->resolveComplexPaths($loadedData, $customFields);

        return $loadedData;
    }

    public function resolveComplexPaths(array $loadedData, Collection $customFields): array
    {
        foreach ($loadedData as $key => $data) {
            if (!str_contains($key, '.')) {
                continue;
            }

            $keyPath = explode('.', $key);
            $identifier = $keyPath[0];
            $path = implode('.', array_slice($keyPath, 1)); //the path without identifier

            $customField = $customFields->get($identifier);
            $pathResolved = $this->resolveFieldPath($customField, $customFields, $path);
            $pathResolved .= '.' . $identifier;

            Arr::set($loadedData, $pathResolved, $data);
            unset($loadedData[$key]);
        }

        return $loadedData;
    }

    protected function resolveFieldPath(CustomField $childField, Collection $formFields, string $path): string
    {
        //ToDo what if an template is in an repeater
        $nearestParent = $this->getNearestParentField($childField, $formFields);

        if (is_null($nearestParent)) {
            return '';
        }

        $pathFragments = explode('.', $path);
        $path = end($pathFragments);
        //Get Last Path element ToDO check if that works???
        $lastPath = sizeof($pathFragments) > 1 ? implode(' ', array_slice($pathFragments, 0, -1)) : null;

        if (!is_null($lastPath)) {
            $resultPathParent = static::resolveFieldPath($nearestParent, $formFields, $lastPath);
            return $resultPathParent . '.' . $nearestParent->identifier . '.' . $path;
        }

        return $nearestParent->identifier . '.' . $path;
    }

    private function isFieldInRange(
        ?CustomField $customField,
        CustomForm $customForm,
        Collection $templateTypeFields,
        ?int $begin,
        ?int $end
    ): bool {
        //If field is in template, get template field for position
        if (!is_null($customField) && $customForm->id !== $customField->custom_form_id) {
            /** @var CustomField $customField */
            $customField = $templateTypeFields->get($customField->custom_form_id);
        }

        if (is_null($customField)) {
            return false;
        }

        $beginCondition = is_null($begin) || $begin <= $customField->form_position;
        $endCondition = is_null($end) || $customField->form_position <= $end;

        return $beginCondition && $endCondition;
    }

    private function getDataIdentifier(CustomFieldAnswer $fieldAnswer, CustomField $customField): string
    {
        $dataIdentifier = $customField->identifier;
        $dataIdentifier .= is_null($fieldAnswer->path) ? '' : '.' . $fieldAnswer->path;
        return $dataIdentifier;
    }

    private function getNearestParentField(CustomField $childField, Collection $formFields): ?CustomField
    {
        $nearestParent = [];
        foreach ($formFields as $field) {
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

        return $nearestParent;
    }
}
