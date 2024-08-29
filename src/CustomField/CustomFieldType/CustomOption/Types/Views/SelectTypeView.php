<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;

class SelectTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView;
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Component {
        $select = static::makeComponent(Select::class, $record)
            ->options(FieldMapper::getAvailableCustomOptions($record));

        if(FieldMapper::getOptionParameter($record,"several")){
            $maxItems = FieldMapper::getOptionParameter($record,"max_select");
            $select->multiple()->minItems($record->required?FieldMapper::getOptionParameter($record,"min_select"):0);
            if($maxItems > 0)$select->maxItems($maxItems);
        }

        return $select;
    }

}
