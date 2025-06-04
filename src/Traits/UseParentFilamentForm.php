<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Filament\Forms\Components\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

trait UseParentFilamentForm
{
    public function getFilamentForm(Component $component, HasForms $livewire): ?Form
    {
        $statePath = $component->getStatePath();
        $firstPath = explode('.', $statePath)[0];
        return collect($livewire->getCachedForms())
            ->firstWhere(fn(Form $form) => $form->getStatePath() === $firstPath);
    }
}
