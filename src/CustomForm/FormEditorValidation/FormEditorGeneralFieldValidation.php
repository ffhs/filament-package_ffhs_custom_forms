<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormEditorValidation;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;

class FormEditorGeneralFieldValidation extends FormEditorValidation
{
    public function repeaterValidation(CustomForm $record, Closure $fail, array $value, string $attribute):void {
        $formIdentifier = $record->custom_form_identifier;
        $requiredGeneralFieldForm = GeneralFieldForm::query()
            ->where("custom_form_identifier", $formIdentifier)
            ->select("general_field_id")
            ->where("is_required", true)
            ->with("generalField")
            ->get();

        $requiredGeneralIDs = $requiredGeneralFieldForm
            ->map(fn ($fieldForm) => $fieldForm->general_field_id);

        $usedGeneralIDs =EditCustomFormHelper::getUsedGeneralFieldIds($value);
        $notAddedRequiredFields = $requiredGeneralIDs
            ->filter(fn($id)=> !in_array($id, $usedGeneralIDs));

        if($notAddedRequiredFields->count() == 0) return;

        $fieldName = $requiredGeneralFieldForm
            ->filter(function($fieldForm) use ($notAddedRequiredFields) {
                $generalFieldId = $fieldForm->general_field_id;
                $notAddedField = $notAddedRequiredFields->first();
                return $generalFieldId == $notAddedField;
            })
            ->first()->generalField->name_de;

        $failureMessage =
            "Du must das generelle Feld \"" . $fieldName . "\" hinzufügen"; //ToDo Translate

        $fail($failureMessage);
    }

}
