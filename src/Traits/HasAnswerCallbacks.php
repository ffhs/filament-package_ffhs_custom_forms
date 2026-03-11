<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Illuminate\Support\Collection;

trait HasAnswerCallbacks
{
    public function afterAnswerFieldSave(CustomFieldAnswer $field, array $formData): void
    {
    }

    public function updateAnswerFormComponentOnSave(
        Component $component,
        CustomField $customField,
        Schema $schema,
        Collection $flattenFormComponents
    ): void {
        //You can interact with the Component like in FileUpload
    }

    public function prepareToSaveAnswerData(EmbedCustomFieldAnswer $answer, mixed $data): ?array
    {
        if (is_null($data)) {
            return null;
        }

        return ['saved' => $data];
    }

    public function prepareLoadAnswerData(EmbedCustomFieldAnswer $answer, ?array $data): mixed
    {
        if (is_null($data) || !array_key_exists('saved', $data) || is_null($data['saved'])) {
            return null;
        }

        return $data['saved'];
    }

    public function isEmptyAnswer(CustomFieldAnswer $customFieldAnswer, ?array $fieldAnswererData): bool
    {
        if (is_null($fieldAnswererData)) {
            return true;
        }

        if (count($fieldAnswererData) === 0) {
            return true;
        }

        if (count($fieldAnswererData) === 1 && array_key_exists('saved', $fieldAnswererData)) {
            return $this->isRawAnswerEmpty($fieldAnswererData['saved']);
        }

        return false;
    }

    private function isRawAnswerEmpty(mixed $answerData): bool
    {
        if (is_null($answerData)) {
            return true;
        }

        if (is_string($answerData) && empty($answerData)) {
            return true;
        }

        if (is_array($answerData) && empty($answerData)) {
            return true;
        }

        return false;
    }
}
