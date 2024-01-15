<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Filament\Forms\Components\TextInput;

trait HasColumnSpan
{

    public function getExtraOptionFields(): array {
        return [
            'column_span' => 3
        ];
    }

    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("column_span")
                ->label("Zeilenweite")//ToDo Translation
                ->step(1)
                ->integer()
                ->minValue(1)
                ->maxValue(10)
                ->required()
        ];
    }

}
