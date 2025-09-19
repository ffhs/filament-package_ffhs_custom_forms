<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;
use Illuminate\Support\Facades\Lang;

class TextTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        /** @var TextInput $input */
        $input = $this->makeComponent(TextInput::class, $customField, false);
        $suggestions = $this->getOptionParameter($customField, 'suggestions');

        if (!empty($suggestions) && !empty($suggestions[Lang::locale()])) {
            $suggestionsList = array_map(fn($data) => $data['value'] ?? '', $suggestions[Lang::locale()]);
            $input->datalist($suggestionsList);
        }

        return $input;
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        if ($customFieldAnswer->getCustomField()->id == 20) {
            dd($this->getAnswer($customFieldAnswer), $customFieldAnswer);
        }
        return $this->makeComponent(TextEntry::class, $customFieldAnswer, true);
    }
}
