<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Support\Icons\Heroicon;

class DefaultCustomFieldDeleteAction extends FieldTypeAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->closeModalByClickingAway(false)
            ->icon(Heroicon::Trash)
            ->requiresConfirmation()
            ->color('danger')
            ->modalHeading(function (CustomFieldType $type) {
                $fieldTypeName = $type->getTranslatedName();
                $fieldName = $fieldData['name'][app()->getLocale()] ?? ''; //ToDo Translation
                $parameters = ['name' => $fieldName, 'type' => $fieldTypeName];

                return trans(CustomField::__('actions.delete.confirmation_message'), $parameters);
            })
            ->action(function ($get, $set, $value) {
                $state = $get('../');
                unset($state[$value]);
                $set('../', $state);
            });
    }
}
