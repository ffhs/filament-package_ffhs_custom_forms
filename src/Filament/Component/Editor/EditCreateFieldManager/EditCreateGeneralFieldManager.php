<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

final class  EditCreateGeneralFieldManager extends EditCreateFieldManager
{

    public static function getFieldData(EditCustomFormFields $editField, array $status, array $arguments, string $key): array{
        $generalFieldId = $arguments["value"];

        $generalField = GeneralField::cached($generalFieldId);


        return [
            "custom_form_id" => $editField->getRecord()->id,
            "general_field_id" => $generalFieldId,
            "options" => $generalField->getType()->getDefaultTypeOptionValues(),
            "is_active" => true,
        ];
    }

}
