<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm;

use Filament\Forms\Components\Select;

class CustomFormTypeSelector extends Select
{
    public static function make(string $name = 'custom_form_identifier'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->label("Formularart") //ToDo Translate
            ->options($this->getTypeOptions());
    }


    protected function getTypeOptions(): array{
        $keys = array_map(fn($config) => $config::identifier(),config("ffhs_custom_forms.forms"));
        $values = array_map(fn($config) => $config::displayName(),config("ffhs_custom_forms.forms"));
        return array_combine($keys,$values);
    }


}
