<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Filament\Forms\Components\Actions\Action;

class DefaultCustomFieldDeleteAction extends Action
{
    protected function setUp(): void {

       parent::setUp();

       $this->iconButton();
       $this->icon('heroicon-c-trash');
       $this->color('danger');

       $this->closeModalByClickingAway(false);

        //ToDo Confirm Message
       $this->requiresConfirmation();



    }


}
