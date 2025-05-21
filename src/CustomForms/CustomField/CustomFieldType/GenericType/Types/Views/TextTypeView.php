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
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Lang;

class TextTypeView implements FieldTypeView
{

    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        $input = static::makeComponent(TextInput::class, $record);

        $suggestions = FieldMapper::getOptionParameter($record, "suggestions");
        if (!empty($suggestions) && !empty($suggestions[Lang::locale()])) {
            $suggestionsList = array_map(fn($data) => $data["value"] ?? "", $suggestions[Lang::locale()]);
            $input->datalist($suggestionsList);
        }


        return $input;
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        return static::makeComponent(TextEntry::class, $record);
    }

}
