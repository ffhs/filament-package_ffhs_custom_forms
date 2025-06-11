<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\KeyValue;
use Filament\Infolists\Components\KeyValueEntry;

class KeyValueTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        return $this->makeComponent(KeyValue::class, $record)
            ->editableKeys($this->getOptionParameter($record, "editableKeys"))
            ->editableValues($this->getOptionParameter($record, "editableValues"));
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $answerer = $this->getAnswer($record);
        $answerer = empty($answerer) ? "" : $answerer;

        return $this->makeComponent(KeyValueEntry::class, $record)
            ->state($answerer);
    }
}
