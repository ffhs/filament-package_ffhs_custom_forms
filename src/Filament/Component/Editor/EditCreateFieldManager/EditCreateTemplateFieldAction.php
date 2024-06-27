<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

final class  EditCreateTemplateFieldAction extends EditCreateFieldAction
{
    public function createField(array $arguments, $set, $state, $component,$get){

        $field = [
            "template_id" =>  $arguments["value"],
            "is_active" => true,
        ];

       $this->addNewField($arguments, $field, $state, $set, $component);
    }

}
