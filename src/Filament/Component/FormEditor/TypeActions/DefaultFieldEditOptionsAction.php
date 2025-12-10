<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\EditTypeOptionModal;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Schemas\Components\Utilities\Set;

class DefaultFieldEditOptionsAction extends FieldTypeAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->iconButton()
            ->closeModalByClickingAway(false)
            ->action(function (Set $set, array $data) {
                $set('options', $data['options']);
            })
            ->fillForm(fn(array $state) => $state)
            ->schema(fn() => once(fn() => [EditTypeOptionModal::make()]))
            ->icon('carbon-settings-edit')
            ->visible(fn(?CustomFieldType $type) => count($type?->extraTypeOptions() ?? []))
            ->modalHeading(fn(array $state, CustomFormConfiguration $formConfiguration) => once(function () use (
                $state,
                $formConfiguration
            ) {

                $genFieldName = static fn() => $formConfiguration
                    ->getAvailableGeneralFields()
                    ->find($state['general_field_id'])
                    ->name;

                $modalHeading = CustomField::__('actions.edit_options.modal_heading');
                $name = empty($state['general_field_id'])
                    ? ($state['name'][app()->getLocale()] ?? '') //ToDo Translate $record->getLocale()
                    : ('G. ' . $genFieldName());

                return trans($modalHeading, ['name' => $name]);
            }));
    }
}
