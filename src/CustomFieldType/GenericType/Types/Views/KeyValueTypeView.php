<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\KeyValue;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Support\Components\Component;

class KeyValueTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        return $this
            ->makeComponent(KeyValue::class, $customField, false)
            ->editableKeys($this->getOptionParameter($customField, 'editableKeys'))
            ->editableValues($this->getOptionParameter($customField, 'editableValues'));
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $answerer = $this->getAnswer($customFieldAnswer);
        $answerer = empty($answerer) ? '' : $answerer;

        return $this
            ->makeComponent(KeyValueEntry::class, $customFieldAnswer, true)
            ->state($answerer);
    }
}
