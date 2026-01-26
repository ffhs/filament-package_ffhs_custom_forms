<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Support\Icons\Heroicon;

class DefaultFieldDeleteAction extends FieldTypeAction
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
                $fieldTypeName = $type->displayname();
//                $fieldName = $fieldData['name'][app()->getLocale()] ?? ''; //ToDo Fix
                $fieldName = '';
                $parameters = ['name' => $fieldName, 'type' => $fieldTypeName];

                return trans(CustomField::__('actions.delete.confirmation_message'), $parameters);
            })
            ->action(function ($state, $get, $set, $value) {
                unset($state[$value]);
                $set('../', $state);
            });
    }
}
