<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents;

final class CustomFieldAdder extends FormEditorFieldAdder
{
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.field_adder';


    protected function setUp(): void {
        parent::setUp();
        $this->live();
        $this->label(__("filament-package_ffhs_custom_forms::custom_forms.form.compiler.custom_fields"));
    }

    public function getTypes(): array {
        return collect($this->getRecord()->getFormConfiguration()::formFieldTypes())
            ->map(fn($class) => new $class())->toArray();
    }

}
