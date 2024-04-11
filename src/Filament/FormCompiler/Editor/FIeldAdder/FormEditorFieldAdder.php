<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FIeldAdders;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Livewire\Component;

abstract class FormEditorFieldAdder extends Component
{
    protected string $view = 'filament-forms::components.group';

    public static function make(CustomForm $form): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }




}
