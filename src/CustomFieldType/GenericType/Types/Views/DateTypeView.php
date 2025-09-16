<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;

class DateTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): DatePicker {
        return $this
            ->makeComponent(DatePicker::class, $record, false)
            ->format($this->getOptionParameter($record, 'format'));
    }

    public function getEntryComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): TextEntry {
        return $this
            ->makeComponent(TextEntry::class, $record, true)
            ->dateTime($this->getOptionParameter($record, 'format'));
    }
}
