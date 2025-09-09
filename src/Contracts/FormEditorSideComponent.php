<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Filament\Schemas\Components\Component;

interface FormEditorSideComponent
{
    public static function make(): static;

    public static function siteComponent(CustomFormConfiguration $configuration): Component;

}
