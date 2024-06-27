<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

final class  EditCreateGeneralFieldAction extends EditCreateFieldAction
{

    public function createField(array $arguments, $set, $state, $component){
        $generalFieldId = $arguments["value"];

        $generalField = GeneralField::cached($generalFieldId);


        $field = [
            "general_field_id" => $generalFieldId,
            "options" => $generalField->getType()->getDefaultTypeOptionValues(),
            "is_active" => true,
        ];

        $this->addNewField($arguments, $field, $state, $set, $component);
    }
}
