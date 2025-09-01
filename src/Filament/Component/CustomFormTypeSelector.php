<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;

class CustomFormTypeSelector extends Select
{
    public static function make(?string $name = 'custom_form_identifier'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(CustomForm::__('attributes.custom_form_identifier'))
            ->options($this->getTypeOptions());
    }

    protected function getTypeOptions(): Collection|array
    {
        return collect(CustomForms::getFormConfigurations())
            ->map(fn(CustomFormConfiguration $configuration) => $configuration::displayName());
    }
}
