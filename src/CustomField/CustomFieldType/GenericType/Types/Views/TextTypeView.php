<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Lang;

class TextTypeView implements FieldTypeView
{

    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): TextInput {

        $input = static::makeComponent(TextInput::class, $record)
            ->maxLength(FieldMapper::getOptionParameter($record,"max_length"))
            ->minLength(FieldMapper::getOptionParameter($record,"min_length"));

        $suggestions = FieldMapper::getOptionParameter($record,"suggestions");
        if(!empty($suggestions) && !empty($suggestions[Lang::locale()])) {
            $suggestionsList = array_map(fn($data) => $data["value"] ?? "", $suggestions[Lang::locale()]);
            $input->datalist($suggestionsList);
        }

        $mask = FieldMapper::getOptionParameter($record,"alpine_mask");
        if(!empty($mask)) $input = $input->mask($mask);

        return $input;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): TextEntry {
        return static::makeComponent(TextEntry::class, $record);
    }

}
