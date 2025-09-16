<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\KeyValue;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Support\Components\Component;

class KeyValueTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        return $this
            ->makeComponent(KeyValue::class, $record, false)
            ->editableKeys($this->getOptionParameter($record, 'editableKeys'))
            ->editableValues($this->getOptionParameter($record, 'editableValues'));
    }

    public function getEntryComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        $answerer = $this->getAnswer($record);
        $answerer = empty($answerer) ? '' : $answerer;

        return $this
            ->makeComponent(KeyValueEntry::class, $record, true)
            ->state($answerer);
    }
}
