<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\OptionLikeAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Components\EditFieldRuleModal;
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
