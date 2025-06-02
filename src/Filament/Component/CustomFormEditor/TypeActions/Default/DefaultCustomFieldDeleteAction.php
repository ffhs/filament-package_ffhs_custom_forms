<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\FieldTypeAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanModifyCustomFormEditorData;

class DefaultCustomFieldDeleteAction extends FieldTypeAction
{
    use CanModifyCustomFormEditorData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();
        $this->icon('heroicon-c-trash');
        $this->color('danger');

        $this->closeModalByClickingAway(false);

        $this->requiresConfirmation();
        $this->modalHeading(function (CustomFieldType $fieldType, array $fieldData, CustomForm $record) {
            $fieldTypeName = $fieldType->getTranslatedName();
            $fieldName = $fieldData['name'][$record->getLocale()] ?? '';
            $parameters = ['name' => $fieldName, 'type' => $fieldTypeName];
            return trans(CustomField::__('actions.delete.confirmation_message'), $parameters);
        });

        $this->action(function ($get, $set, string $fieldKey) {
            //Delete Structure
            $state = $this->removeFieldFromEditorData($fieldKey, $get('.'));
            $set('.', $state);
        });
    }
}
