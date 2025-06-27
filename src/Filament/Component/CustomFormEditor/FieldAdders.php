<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\Default\CustomFieldTypeAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Fieldset;

class FieldAdders extends Fieldset
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->columnStart(1)
            ->columnSpan(1)
            ->columns(1)
            ->schema($this->getAdders(...));
    }

    protected function getAdders(?CustomForm $record): array
    {
        if (is_null($record)) {
            return [];
        }

        return once(static fn() => collect($record->getFormConfiguration()::editorFieldAdder())
            ->map(fn(string|CustomFieldTypeAdder $class) => $class::make())
            ->toArray());
    }
}
