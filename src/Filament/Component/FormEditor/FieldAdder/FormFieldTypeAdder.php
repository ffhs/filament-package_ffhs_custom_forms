<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Support\Components\Component;

class FormFieldTypeAdder extends FormFieldAdder
{
    public static function getSiteComponent(CustomFormConfiguration $configuration): Component
    {
        return self::make('add_type_node')
            ->formConfiguration($configuration);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->columns(2)
            ->options($this->getTypeOptions(...))
            ->label(CustomForm::__('pages.type_adder.label'))
            ->optionIcons($this->getTypeOptionIcons(...))
            ->action($this->onAdd(...));
    }

    protected function getTypeOptions(): mixed
    {
        return once(function () {
            return collect($this->getFormConfiguration()->getSelectableFieldTypes())
                ->map(fn(CustomFieldType $fieldType) => $fieldType->displayname());
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
