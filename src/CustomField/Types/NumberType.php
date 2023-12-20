<?php

namespace App\Domain\CustomField\Types;

use App\Models\CustomField;
use Filament\Forms\Components\TextInput;

class NumberType extends TextType
{
    public static function getFieldName(): string {return "number";}

    public function getFormComponent(CustomField $record,string $viewMode = "default", array $parameter = []): TextInput {
        return parent::getFormComponent($record)
            ->step($record->field_options["step"])
            ->numeric();
    }


    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("step") // ToDo: Translate
                ->numeric()
                ->placeholder(1)
                ->step(1)
        ];
    }

    public function getExtraOptionFields(): array {
        return [
            'step'=>null,
        ];
    }

}
