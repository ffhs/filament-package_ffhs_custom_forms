<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents;


use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\EditRecord;

abstract class FormEditorFieldAdder extends Group
{
    public static function addNewField($component, array $arguments, EditRecord $livewire, array $fieldData): void
    {
        //Check if arguments exist
        if (empty($arguments["position"])) {
            return;
        }
        if (is_null($arguments["stateWithField"])) {
            return;
        }
        if (empty($arguments["temporaryKey"])) {
            return;
        }

        $position = $arguments['position'];
        $temporaryKey = $arguments["temporaryKey"];
        $state = $arguments['stateWithField'];

        $posEndArgument = CustomField::getEndContainerPositionAttribute();
        $posArgument = CustomField::getPositionAttribute();


        $type = CustomForms::getFieldTypeFromRawDate($fieldData);

        $fieldData[$posArgument] = $position;
        if ($type instanceof CustomLayoutType) {
            $fieldData[$posEndArgument] = $position;
        }

        $path = $component->getStatePath() . '.custom_fields';

        unset($state[$temporaryKey]);
        $state[uniqid()] = $fieldData;

        $component->getSetCallback()($path, $state, true);
        $livewire->updatedInteractsWithForms($path);
    }

    public function getChildComponents(): array
    {
        return array_merge([
            Placeholder::make($this->getLabel())
        ], parent::getChildComponents());
    }

    public static function make(array|\Closure $schema = []): static
    {
        $static = app(static::class);
        $static->configure();
        return $static;
    }

}
