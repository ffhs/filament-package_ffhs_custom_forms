<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FieldAdder;

use Ffhs\FfhsUtils\Filament\Components\DragDrop\DragDropSelectAction;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FormEditorSideComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormGroupName;
use Filament\Forms\Components\Concerns\CanGenerateUuids;

abstract class FormFieldAdder extends DragDropSelectAction implements FormEditorSideComponent
{
    use HasFormConfiguration;
    use CanGenerateUuids;
    use HasFormGroupName;

    public function addNewField(array $fieldData): void
    {
        $arguments = $this->getArguments();
        $path = $arguments['path'];
        $position = $arguments['position'];

        $get = $this->getSchemaComponent()->makeGetUtility();
        $set = $this->getSchemaComponent()->makeSetUtility();
        $fieldData['order'] = $position;

        $state = $get($path, true) ?? [];
        $fields = collect($state)
            ->map(function ($field) use ($position) {
                if ($field['order'] >= $position) {
                    $field['order'] += 1;
                }
                return $field;
            })
            ->put($this->generateUuid(), $fieldData)
            ->sortBy('order')
            ->toArray();

        $set($path, $fields, true);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->dragGroup($this->getGroupName(...));
    }

    protected function getCustomFieldsState(): array
    {
        $fields = $this->getSchemaComponent()?->getState()['custom_fields'] ?? [];
        return new CustomFieldStateCast()->flattCustomFields($fields);
    }

}
