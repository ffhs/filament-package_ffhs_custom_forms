<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Filament\Forms\Components\Actions\Action;

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


        if(!empty($beforeField)) $position = $beforeField["form_position"];
        else if(!empty($inField)) $position = $inField["form_position"] + 1;
        else $position = 1;

        Debugbar::info($inField, $beforeField,$position);

        $key = $fieldData['identifier'] ?? uniqid();
        $fieldData["custom_form_id"] = $component->getRecord()->id;
        $newState = EditCustomFormHelper::addField($fieldData, $key, $position, $state);

        $set($component->getStatePath(false), $newState);
    }

}
