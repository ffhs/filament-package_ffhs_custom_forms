<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\FormRuleAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait CanLoadFormAnswer
{
    public function modifyFieldDataFormRules(EmbedCustomFieldAnswer $answer, mixed $fieldData, $formRules): mixed
    {
        foreach ($formRules as $rule) {
            /**@var Rule $rule */
            $fieldData = $rule->handle(FormRuleAction::LoadData, ['field_answerer' => $answer],
                $fieldData); //ToDo Maby remove
        }

        return $fieldData;
    }

    public function loadCustomAnswerForEntry(
        EmbedCustomFormAnswer $answer,
        int|null $begin = null,
        int|null $end = null,
        ?EmbedCustomForm $customForm = null
    ): array {
        return $this->loadCustomAnswerData($answer, $begin, $end, $customForm, false);
    }

    public function loadCustomAnswerData(
        EmbedCustomFormAnswer $answer,
        int|null $begin = null,
        int|null $end = null,
        ?EmbedCustomForm $customForm = null,
        bool $withRules = true
    ): array {
        $loadedData = [];
        $customForm = $customForm ?? $answer->getCustomForm();
        $customFields = $this->getCustomFieldsCollection($customForm);
        $identifierTemplateMap = $this->buildIdentifierTemplateMap($customFields);
        $formRules = $withRules ? $customForm->getRules() : null;

        foreach ($answer->getCustomFieldAnswers() as $fieldAnswer) {
            $customField = $fieldAnswer->getCustomField();

            if (!$this->isFieldInRange($customField, $identifierTemplateMap, $begin, $end)) {
                continue;
            }

            $fieldData = $withRules
                ? $this->processFieldDataWithRules($fieldAnswer, $customField, $formRules)
                : $fieldAnswer->answer;

            $dataIdentifier = $this->getDataIdentifier($fieldAnswer, $customField);
            $loadedData[$dataIdentifier] = $fieldData;
        }

        return $this->resolveComplexPaths($loadedData, $customFields);
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

            /**@var ?EmbedCustomField $customField */
            $customField = $customFields->get($identifier);

            if (is_null($customField)) {
                continue;
            }

            $pathResolved = $this->resolveFieldPath($customField, $customFields, $path);
            $pathResolved .= '.' . $identifier;

            Arr::set($loadedData, $pathResolved, $data);
            unset($loadedData[$key]);
        }
        return $loadedData;
    }

    protected function resolveFieldPath(EmbedCustomField $childField, Collection $formFields, string $path): string
    {
        //ToDo what if an template is in an repeater
        $nearestParent = $this->getNearestParentField($childField, $formFields);

        if (is_null($nearestParent)) {
            return '';
        }

        $pathFragments = explode('.', $path);
        $path = end($pathFragments);
        $lastPath = count($pathFragments) > 1 ? implode(' ', array_slice($pathFragments, 0, -1)) : null;
        $endPath = [];

        if (!is_null($lastPath)) {
            $resultPathParent = static::resolveFieldPath($nearestParent, $formFields, $lastPath);
            if (!empty($resultPathParent)) {
                $endPath[] = $resultPathParent;
            }
        }
        if (!empty($nearestParent->identifier)) {
            $endPath[] = $nearestParent->identifier;
        }
        if (!empty($nearestParent->identifier)) {
            $endPath[] = $path;
        }

        return implode('.', $endPath);
    }

    protected function isFieldInRange(
        ?EmbedCustomField $customField,
        Collection $identifierTemplateMap,
        ?int $begin,
        ?int $end
    ): bool {
        if (is_null($customField)) {
            return false;
        }

        //If field is in template, get template field for position
        $customFieldSolved = $identifierTemplateMap->get($customField->identifier, $customField);

        $beginCondition = is_null($begin) || $begin <= $customFieldSolved->form_position;
        $endCondition = is_null($end) || $customFieldSolved->form_position <= $end;

        return $beginCondition && $endCondition;
    }

    private function getCustomFieldsCollection(EmbedCustomForm $customForm): Collection
    {
        return $customForm
            ->getCustomFields()
            ->mapWithKeys(fn(EmbedCustomField $field) => [$field->identifier => $field])
            ->sortBy(fn($item) => $item->form_position);
    }

    private function buildIdentifierTemplateMap(Collection $customFields): Collection
    {
        return $customFields
            ->whereNotNull('template_id')
            ->keyBy('template_id')
            ->map(function (EmbedCustomField $template) {
                return $template->getTemplate()
                    ?->getOwnedFields()
                    ->mapWithKeys(fn(EmbedCustomField $field) => [$field->identifier => $template]);
            })->collapse();
    }

    private function processFieldDataWithRules(
        EmbedCustomFieldAnswer $fieldAnswer,
        EmbedCustomField $customField,
        $formRules
    ): mixed {
        $fieldData = $customField
            ->getType()
            ?->prepareLoadAnswerData($fieldAnswer, $fieldAnswer->answer);

        return $this->modifyFieldDataFormRules($fieldAnswer, $fieldData, $formRules);
    }

    private function getDataIdentifier(EmbedCustomFieldAnswer $fieldAnswer, EmbedCustomField $customField): string
    {
        $dataIdentifier = $customField->identifier;
        $dataIdentifier .= is_null($fieldAnswer->getPath()) ? '' : '.' . $fieldAnswer->getPath();

        return $dataIdentifier;
    }

    private function getNearestParentField(EmbedCustomField $childField, Collection $formFields): ?EmbedCustomField
    {
        $nearestParent = null;
        $nearestParentEnd = null;

        foreach ($formFields as $field) {
            if ($field->form_position >= $childField->form_position) {
                break;
            }
            if (!$field->getType()?->hasSplitFields()) {
                continue;
            }

            if ($field->layout_end_position < $childField->form_position) {
                continue;
            }

            if (!is_null($field->layout_end_position) && $field->layout_end_position < $nearestParentEnd) {
                continue;
            }

            $nearestParent = $field;
            $nearestParentEnd = $field->layout_end_position;
        }
        return $nearestParent;
    }
}
