<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\Components\EditTypeOptionModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\OptionLikeAction;

class DefaultCustomFieldEditTypeOptionsAction extends OptionLikeAction
{
    protected function setUp(): void {

       parent::setUp();

       $this->icon('carbon-settings-edit');
       //Hidde if it hasn't any options
       $this->visible(function($get, $arguments) {
           if(!array_key_exists('item', $arguments)) return false;
           return CustomFieldUtils::getFieldTypeFromRawDate($get($arguments['item']))?->extraTypeOptions() ?? false;
       });
        $this->closeModalByClickingAway(false);
       $this->form([
           EditTypeOptionModal::make()
       ]);
    }


    protected function getTitleName(): string {
       return "Field";
    }
}
