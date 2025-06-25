<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\EditRecord;

abstract class FormEditorFieldAdder extends Component
{
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom-form-editor.field-adder';

    final public function __construct(array|Closure $schema = [])
    {
        $this->schema($schema);
    }

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();
        return $static;
    }


    public static function addNewField(
        Component $component,
        array $arguments,
        EditRecord $livewire,
        array $fieldData
    ): void {
        //Check if arguments exist
        if (empty($arguments['position'])) {
            return;
        }
        if (is_null($arguments['stateWithField'])) {
            return;
        }
        if (empty($arguments['temporaryKey'])) {
            return;
        }

        $position = $arguments['position'];
        $temporaryKey = $arguments['temporaryKey'];
        $state = $arguments['stateWithField'];
        $path = $component->getStatePath() . '.custom_fields';

        $posEndArgument = CustomField::getEndContainerPositionAttribute();
        $posArgument = CustomField::getPositionAttribute();
        $type = CustomForms::getFieldTypeFromRawDate($fieldData, $component->getRecord());

        $fieldData[$posArgument] = $position;
        if ($type instanceof CustomLayoutType) {
            $fieldData[$posEndArgument] = $position;
        }

        unset($state[$temporaryKey]);
        $state[uniqid()] = $fieldData;

        $component->getSetCallback()($path, $state, true);
        $livewire->updatedInteractsWithForms($path);
    }

    public function getChildComponents(): array
    {
        return [
            Placeholder::make($this->getLabel()),
            ...parent::getChildComponents()
        ];
    }
}
