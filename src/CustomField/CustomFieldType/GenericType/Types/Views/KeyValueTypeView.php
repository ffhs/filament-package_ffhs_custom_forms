<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\KeyValue;
use Filament\Infolists\Components\KeyValueEntry;

class KeyValueTypeView implements FieldTypeView
{

    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        return static::makeComponent(KeyValue::class, $record)
            ->editableKeys(FieldMapper::getOptionParameter($record, "editableKeys"))
            ->editableValues(FieldMapper::getOptionParameter($record, "editableValues"));
    }


    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $answerer = FieldMapper::getAnswer($record);
        $answerer = empty($answerer) ? "" : $answerer;

        return static::makeComponent(KeyValueEntry::class, $record)
            ->state($answerer);
    }
}
