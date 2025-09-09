<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FieldAdder;

use Ffhs\FfhsUtils\Filament\DragDrop\DragDropSelectAction;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Filament\Forms\Components\Concerns\CanGenerateUuids;

abstract class FormFieldAdder extends DragDropSelectAction
{
    use HasFormConfiguration;
    use CanGenerateUuids;

    public function addNewField(
        array $fieldData
    ): void {
        $arguments = $this->getArguments();
        $path = $arguments['path'];
        $position = $arguments['position'];
//        $type = CustomForms::getFieldTypeFromRawDate($fieldData, $this->getRecord()); //ToDo Change
        $get = $this->getSchemaComponent()->makeGetUtility();
        $set = $this->getSchemaComponent()->makeSetUtility();

        $state = $get($path, true) ?? [];

        // Split the array into two parts
        $before = array_slice($state, 0, $position, true);
        $after = array_slice($state, $position, null, true);

// Merge with the new element in between
        $arr = $before + [$this->generateUuid() => $fieldData] + $after;

        $set($path, $arr, true);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

}
