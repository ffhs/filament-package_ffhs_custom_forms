<?php

namespace App\Domain\CustomField\Types;

use App\Domain\CustomField\CustomFieldType;
use App\Models\CustomField;
use App\Models\CustomFieldAnswer;
use App\Models\CustomFieldProductTerm;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\App;

class CheckboxType extends CustomFieldType
{
    public static function getFieldName(): string { return "checkbox"; }


    public function getFormComponent(CustomField $record, string $viewMode = "default",
        array $parameter = []): Component {
        return Checkbox::make($record->identify_key)
            ->label(self::getLabelName($record->customField))
            ->helperText(self::getToolTips($record));
    }

    public function getViewComponent(CustomFieldAnswer $record, string $viewMode = "default",
        array $parameter = []): \Filament\Infolists\Components\Component {
        return IconEntry::make($record->customField->identify_key)
            ->label(self::getLabelName($record->customField))
            ->state($record->answare)
            ->inlineLabel()
            ->boolean();
    }
}
