<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;

final class  EditCreateTypeFieldAction extends EditCreateFieldAction
{

    public function createField(array $arguments,  $set, EditCustomFormFields $component){
        $type = CustomFieldType::getTypeFromIdentifier($arguments["value"]);

        $field = [
            "identifier" => uniqid(),
            "type" => $type::identifier(),
            "options" => $type->getDefaultTypeOptionValues(),
            "is_active" => true,
        ];

        $this->addNewField($component, $set, $arguments, $field);
    }

}
