<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Filament\Support\Components\Component;

interface FormEditorSideComponent
{
    public static function getSiteComponent(CustomFormConfiguration $configuration): Component;
}
