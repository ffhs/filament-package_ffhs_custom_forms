<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;
use Filament\Forms\Set;

abstract class EditCreateFieldAction extends Action
{
    //Needs to implement createField()
    protected function setUp(): void {
        parent::setUp();
        $this->action($this->createField(...));
    }

    protected function addNewField(EditCustomFormFields $component, array $arguments, array $fieldData): void {
        $pos = $arguments['formPosition'];

        $path = $component->getStatePath();
        $state = $component->getGetCallback()($path, true);

        $state = EditCustomFormHelper::addField($fieldData, $pos , $state);

        $component->getSetCallback()($path, $state, true);
    }

}
