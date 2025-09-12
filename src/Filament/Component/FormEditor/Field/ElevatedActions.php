<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field;

use Filament\Schemas\Components\Actions;

class ElevatedActions extends Actions //ToDo move to utils
{
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.elevated_actions';

    protected function setUp(): void
    {
        $this->alignEnd();
    }

}
