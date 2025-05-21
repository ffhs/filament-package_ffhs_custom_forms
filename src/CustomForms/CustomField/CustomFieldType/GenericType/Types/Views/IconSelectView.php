<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconSelectView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array  $parameter = []): Component {
        return static::makeComponent(IconPicker::class, $record);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array  $parameter = []): \Filament\Infolists\Components\Component {
        return static::makeComponent(IconEntry::class, $record)->icon(FieldMapper::getAnswer($record));
    }

}
