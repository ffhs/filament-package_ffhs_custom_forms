<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\EditFieldRuleModal;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

class DefaultCustomRulesAction extends OptionLikeAction
{
    protected function setUp(): void {

        parent::setUp();

        $this->icon('carbon-rule');
        $this->form([
            EditFieldRuleModal::make()
        ]);

    }

    protected function getTitleName(): string {
        return "Feldregeln";
    }
}
