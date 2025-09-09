<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Support\Components\Component;
use Filament\Forms\Form;
use Illuminate\Support\Collection;

trait HasAnswerCallbacks
{
    public function afterAnswerFieldSave(CustomFieldAnswer $field, array $formData): void
    {
    }

    public function updateAnswerFormComponentOnSave(
        Component $component,
        CustomField $customField,
        Form $form,
        Collection $flattenFormComponents
    ): void {
        //You can interact with the Component like in FileUpload
    }

    public function prepareToSaveAnswerData(CustomFieldAnswer $answer, mixed $data): ?array
    {
        if (is_null($data)) {
            return null;
        }

        return ['saved' => $data];
    }

    public function prepareLoadAnswerData(CustomFieldAnswer $answer, ?array $data): mixed
    {
        if (is_null($data) || !array_key_exists('saved', $data) || is_null($data['saved'])) {
            return null;
        }

        return $data['saved'];
    }

    public function isEmptyAnswer(CustomFieldAnswer $customFieldAnswer, ?array $fieldAnswererData): bool
    {
        return empty($fieldAnswererData)
            || (empty($fieldAnswererData['saved'] ?? [])
                && sizeof($fieldAnswererData) === 1
                && !is_bool($fieldAnswererData['saved']));
    }
}
