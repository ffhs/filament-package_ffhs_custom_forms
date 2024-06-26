<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;

final class  EditCreateTypeFieldManager extends EditCreateFieldManager
{

    public static function getFieldData(EditCustomFormFields $editField, array $status, array $arguments, string $key): array{
        $type = CustomFieldType::getTypeFromIdentifier($arguments["value"]);

        return [
            "custom_form_id" => $editField->getRecord()->id,
            "identifier" => $key,
            "type" => $type::identifier(),
            "options" => $type->getDefaultTypeOptionValues(),
            "is_active" => true,
        ];
    }

}
