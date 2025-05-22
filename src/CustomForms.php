<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\DataManagment\HasCustomFormSaveDataManagement;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanInteractionWithFieldTypes;
use Illuminate\Support\Collection;

class CustomForms
{
    use HasCustomFormSaveDataManagement;
    use CanInteractionWithFieldTypes;

    public function getAllowedGeneralFieldsInFormIdentifier(string $formIdentifier): Collection
    {

        return //Cache::remember($formIdentifier . '_general_fields_allowed_in_form', GeneralField::getCacheDuration(),
            once(
                function () use ($formIdentifier) {

                    $generalFieldForms = GeneralFieldForm::query()->where('custom_form_identifier', $formIdentifier);
                    $generalFields = GeneralField::query()->with('generalFieldForms')->whereIn('id',
                        $generalFieldForms->select('id'))->get();

                    return $generalFields;
                });
    }

}

