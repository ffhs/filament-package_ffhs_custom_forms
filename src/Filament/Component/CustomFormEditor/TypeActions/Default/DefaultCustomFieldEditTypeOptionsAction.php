<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\AdditionalComponents\EditTypeOptionModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\FieldTypeAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanModifyCustomFormEditorData;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;

class DefaultCustomFieldEditTypeOptionsAction extends FieldTypeAction
{
    use CanModifyCustomFormEditorData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();

        $this->closeModalByClickingAway(false);

        $this->action(function (Set $set, array $data, string $fieldKey) {
            $set($fieldKey, $data);
        });
        $this->fillForm(fn(array $fieldData) => $fieldData);

        $this->mutateFormDataUsing(function (Action $action) {
            $forms = array_values($action->getLivewire()->getCachedForms());
            $form = $forms[sizeof($forms) - 1];
            $state = $form->getRawState();
            unset($state["key"]);
            return $state;
        }); //ToDo Try with editor in modal
        $this->modalHeading(function (array $fieldData, CustomForm $record) {
            $modalHeading = CustomField::__('actions.edit_options.modal_heading');
            $name = empty($fieldData['general_field_id'])
                ? ($fieldData['name'][$record->getLocale()] ?? '')
                : ('G. ' . GeneralField::cached($fieldData["general_field_id"])->name); //ToDo Try to do it maybe without cache

            return trans($modalHeading, ['name' => $name]);
        });

        $this->icon('carbon-settings-edit');

        $this->closeModalByClickingAway(false);
        //Hidde if it hasn't any options
        $this->visible(function (?CustomFieldType $fieldType): bool {
            $extraOptions = $fieldType?->extraTypeOptions() ?? [];
            return sizeof($extraOptions) > 0;
        });
        $this->form([
            EditTypeOptionModal::make()
        ]);
    }
}
