<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;

abstract class EditCreateFieldAction extends Action
{
    //Needs to implement createField()
    protected function setUp(): void {
        parent::setUp();
        $this->action($this->createField(...));
    }

    protected function addNewField(array $arguments, array $fieldData, array $state, $set, $component): void {
        $in = $arguments['in'];
        $before = $arguments['before'];

        $beforeField = $state[$before] ?? [];
        $inField = $state[$in] ?? [];

        $key = $fieldData['identifier'] ?? uniqid();

        if(!empty($beforeField)) $position = $beforeField["form_position"];
        else if(!empty($inField)) $position = $inField["form_position"] + 1;
        else $position = 1;

        $fieldData["custom_form_id"] = $component->getRecord()->id;
        $newState = EditCustomFormHelper::addField($fieldData, $key, $position, $state);

        $set($component->getStatePath(false), $newState);
    }

}
