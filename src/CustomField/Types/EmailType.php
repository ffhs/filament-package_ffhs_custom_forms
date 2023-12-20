<?php

namespace App\Domain\CustomField\Types;

use App\Models\CustomField;
use Filament\Forms\Components\TextInput;

class EmailType extends TextType
{
    public static function getFieldName(): string {return "email";}

    public function getFormComponent(CustomField $record,string $viewMode = "default", array $parameter = []): TextInput {
        return parent::getFormComponent($record)
            ->email();
    }



    public function getExtraOptionFields(): array {
        return [];
    }

    public function getExtraOptionSchema(): ?array {
        return null;
    }


}
