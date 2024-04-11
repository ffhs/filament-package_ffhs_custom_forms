<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

class CustomFieldEditModal
{
    public static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state);
        if (!empty($state["general_field_id"])) return 'xl';
        $hasOptions = $type->canBeRequired() || $type->canBeDeactivate() || $type->hasExtraTypeOptions();
        if (!$hasOptions) return 'xl';
        return '5xl';
    }


    public static function getCustomFieldSchema(array $data, CustomForm $customForm):array{
        //ToDo change and import it hier
        return EditCustomFieldForm::getCustomFieldSchema($data,$customForm);
    }


}
