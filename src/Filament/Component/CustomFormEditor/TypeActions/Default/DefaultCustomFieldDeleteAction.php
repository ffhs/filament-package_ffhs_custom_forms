<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
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

        $this
            ->iconButton()
            ->icon('heroicon-c-trash')
            ->color('danger')
            ->closeModalByClickingAway(false)
            ->requiresConfirmation()
            ->modalHeading(function (CustomFieldType $fieldType, array $fieldData, CustomForm $record) {
                $fieldTypeName = $fieldType->getTranslatedName();
                $fieldName = $fieldData['name'][$record->getLocale()] ?? '';
                $parameters = ['name' => $fieldName, 'type' => $fieldTypeName];

                return trans(CustomField::__('actions.delete.confirmation_message'), $parameters);
            })
            ->action(function ($get, $set, string $fieldKey) {
                //Delete Structure
                $state = $this->removeFieldFromEditorData($fieldKey, $get('.'));
                $set('.', $state);
            });
    }
}
