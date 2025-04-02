<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

class TextAreaTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Textarea
    {
        return static::makeComponent(Textarea::class, $record)
            ->autosize(FieldMapper::getOptionParameter($record, "auto_size"));
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        return static::makeComponent(TextEntry::class, $record);
    }
}
