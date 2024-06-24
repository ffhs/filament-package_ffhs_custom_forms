<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Layout;



namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList\EditorCustomFieldList;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

abstract class CustomLayoutType extends CustomFieldType
{
    public function canBeRequired(): bool {
        return false;
    }
    public function hasToolTips(): bool {
        return false;
    }

    public function nameBeforeIconFormEditor(array $state):string {
        $size = empty($state["custom_fields"])?0:sizeof($state["custom_fields"]);
        return new HtmlBadge($size) . parent::nameBeforeIconFormEditor($state);
    }

    public function nameFormEditor(array $state):string {
        return parent::nameFormEditor($state) . '</span>';
    }

    public function editorRepeaterContent(CustomForm $form, array $fieldData): ?array {
        return [EditorCustomFieldList::make($form)];
    }

    public function hasSubFields(): bool {
        return true;
    }

    public function fieldEditorExtraComponent(array $fieldData): ?string {
        return 'filament-package_ffhs_custom_forms::custom_form_edit.extra-type-component.layout';
    }
}
