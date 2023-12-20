<?php

namespace App\Domain\CustomField\Types;

use App\Domain\CustomField\CustomFieldType;
use App\Models\CustomField;
use App\Models\CustomFieldAnswer;
use App\Models\CustomFieldProductTerm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\App;

class TextType extends CustomFieldType
{

    public static function getFieldName(): string {return "text";}


    public function getViewComponent(CustomFieldAnswer $record,string $viewMode = "default",  array $parameter = []): \Filament\Infolists\Components\Component {
        return TextEntry::make($record->customField->identify_key)
            ->state(fn(CustomFieldAnswer $record) => $record->answer)
            ->label(self::getLabelName($record->customField));
    }

    public function getFormComponent(CustomField $record,string $viewMode = "default", array $parameter = []): TextInput {
        return TextInput::make($record->customField()->get()->identify_key)
            ->maxLength($record->field_options["max_size"])
            ->helperText(self::getToolTips($record))
            ->label(self::getLabelName($record));
    }


    public function getExtraOptionFields(): array {
        return [
          'max_size' => 100
        ];
    }

    public function getExtraOptionSchema(): ?array {
        return [
          TextInput::make("max_size")
              ->step(1)
            ->integer()
        ];
    }


}
