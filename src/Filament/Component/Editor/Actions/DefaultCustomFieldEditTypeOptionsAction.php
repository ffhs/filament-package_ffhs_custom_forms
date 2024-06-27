<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\EditTypeOptionModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;

class DefaultCustomFieldEditTypeOptionsAction extends OptionLikeAction
{
    protected function setUp(): void {

       parent::setUp();

       $this->icon('carbon-settings-edit');

       $this->form([
           EditTypeOptionModal::make()
       ]);
    }


    protected function getTitleName(): string {
       return "Feld";
    }
}
