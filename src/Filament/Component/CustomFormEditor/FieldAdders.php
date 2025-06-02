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

        $this->columnStart(1);
        $this->columnSpan(1);
        $this->columns(1);
        $this->schema($this->getAdders(...));
    }

    protected function getAdders(?CustomForm $record): array
    {
        if (is_null($record)) {
            return [];
        }
        return once(static function () use ($record) {
            return collect($record->getFormConfiguration()::editorFieldAdder())
                ->map(fn(string|CustomFieldTypeAdder $class) => $class::make())
                ->toArray();
        });
    }
}
