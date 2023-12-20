<?php

namespace App\Domain\CustomField\Types;

use App\Domain\CustomField\CustomFieldType;
use App\Models\CustomField;
use App\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\App;

class TextAreaType extends CustomFieldType
{

    public static function getFieldName(): string {return "textarea";}

    public function getFormComponent(CustomField $record, string $viewMode = "default",array $parameter = []): Component {
        return Group::make([
            Textarea::make($record->customField()->get()->identify_key)
                ->helperText(self::getToolTips($record))
                ->label(self::getLabelName($record))
        ])->columns(2);
    }

    public function getViewComponent(CustomFieldAnswer $record, string $viewMode = "default",array $parameter = []): \Filament\Infolists\Components\Component {
        return TextEntry::make($record->identify_key)
            ->state($record->answer)
            ->label(self::getLabelName($record->customField))
            ->inlineLabel();
    }
}
