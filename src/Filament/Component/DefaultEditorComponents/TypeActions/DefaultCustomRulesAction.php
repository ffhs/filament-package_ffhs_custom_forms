<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\TypeActions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\TypeActions\OptionLikeAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\EditFieldRuleModal;
use Filament\Support\Enums\MaxWidth;

class DefaultCustomRulesAction extends OptionLikeAction
{
    protected function setUp(): void {

        parent::setUp();

        $this->icon('carbon-rule');
        $this->modalWidth(MaxWidth::ScreenTwoExtraLarge);
        $this->form([
            EditFieldRuleModal::make()
        ]);

    }

    protected function getTitleName(): string {
        return "Feldregeln";
    }
}
