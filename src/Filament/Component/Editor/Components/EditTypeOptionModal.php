<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\UseComponentInjection;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;

class EditTypeOptionModal extends Section
{
    use UseComponentInjection;

    protected string $view = 'filament-forms::components.group';

    protected function setUp(): void {
        parent::setUp();

        $this
            ->schema(fn($state)=>[
                Group::make(CustomFieldUtils::getFieldTypeFromRawDate($state)->getExtraTypeOptionComponents())
                    ->statePath("options")
                    ->columns()
            ]);
    }

}
