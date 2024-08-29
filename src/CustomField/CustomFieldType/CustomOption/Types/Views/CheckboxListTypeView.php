<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Component;

class CheckboxListTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array   $parameter = []): Component {
        return static::makeComponent(CheckboxList::class, $record)
            ->columns(FieldMapper::getOptionParameter($record,"columns"))
            ->options(FieldMapper::getAvailableCustomOptions($record))
            ->maxItems(FieldMapper::getOptionParameter($record,"max_items"))
            ->minItems(FieldMapper::getOptionParameter($record,"min_items"));
    }


}
