<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

class FormFieldTypeAdder extends FormFieldAdder
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->options($this->getTypeOptions(...))
            ->columns(3)
            ->optionIcons($this->getTypeOptionIcons(...))
            ->action($this->onAdd(...));
    }

    protected function getTypeOptions(): mixed
    {
        return once(function () {
            return collect($this->getFormConfiguration()->getSelectableFieldTypes())
                ->map(fn(CustomFieldType $fieldType) => $fieldType->getTranslatedName());
        });
    }

    protected function getTypeOptionIcons(): mixed
    {
        return once(function () {
            return collect($this->getFormConfiguration()->getSelectableFieldTypes())
                ->map(fn(CustomFieldType $fieldType) => $fieldType->icon())
                ->toArray();
        });
    }

    protected function onAdd(array $arguments): void
    {
        /**@var CustomFieldType $type */
        $type = $this->getFormConfiguration()->getSelectableFieldTypes()[$arguments['option']];

        $this->addNewField([
            'identifier' => uniqid(),
            'type' => $type::identifier(),
            'options' => $type->getDefaultTypeOptionValues(),
            'is_active' => true,
            'name' => [
                app()->getLocale() => CustomForm::__('pages.type_adder.new_field_name')
            ]
        ]);
    }

}
