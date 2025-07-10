<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\AdditionalComponents\EditTypeOptionModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\FieldTypeAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanModifyCustomFormEditorData;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;

class DefaultCustomFieldEditTypeOptionsAction extends FieldTypeAction
{
    use CanModifyCustomFormEditorData;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->iconButton()
            ->closeModalByClickingAway(false)
            ->action(function (Set $set, array $data, string $fieldKey) {
                $set($fieldKey, $data);
            })
            ->fillForm(fn(array $fieldData) => $fieldData)
            ->mutateFormDataUsing(function (Action $action) {
                $forms = array_values($action->getLivewire()->getCachedForms());
                $form = $forms[sizeof($forms) - 1];
                $state = $form->getRawState();

                unset($state['key']);

                return $state;
            })
            ->modalHeading(fn(array $fieldData, CustomForm $record) => once(function () use ($fieldData, $record) {
                $genFieldName = static fn() => $record
                    ->getFormConfiguration()
                    ->getAvailableGeneralFields()
                    ->firstWhere('id', $fieldData['general_field_id'])
                    ->name;

                $modalHeading = CustomField::__('actions.edit_options.modal_heading');
                $name = empty($fieldData['general_field_id'])
                    ? ($fieldData['name'][$record->getLocale()] ?? '')
                    : ('G. ' . $genFieldName());

                return trans($modalHeading, ['name' => $name]);
            }))
            ->icon('carbon-settings-edit')
            ->closeModalByClickingAway(false)
            ->visible(function (?CustomFieldType $fieldType): bool {
                $extraOptions = $fieldType?->extraTypeOptions() ?? [];

                return sizeof($extraOptions) > 0;
            })
            ->form(fn() => once(fn() => [EditTypeOptionModal::make()]));
    }
}
