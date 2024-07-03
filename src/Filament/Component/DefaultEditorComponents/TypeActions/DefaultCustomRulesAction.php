<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\TypeActions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\TypeActions\OptionLikeAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\EditFieldRuleModal;

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
