<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;

class CheckboxTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Checkbox
    {
        /**@var $checkbox Checkbox */
        $checkbox = static::makeComponent(Checkbox::class, $record);
        return $checkbox;
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): IconEntry {
        /**@var $iconEntry IconEntry */
        $iconEntry = static::makeComponent(IconEntry::class, $record);

        return $iconEntry
            ->state(is_null(FieldMapper::getAnswer($record)) ? false : FieldMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }

}
