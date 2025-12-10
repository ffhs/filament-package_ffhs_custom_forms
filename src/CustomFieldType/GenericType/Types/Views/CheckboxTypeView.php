<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;

class CheckboxTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Checkbox
    {
        /**@var $checkbox Checkbox */
        $checkbox = $this->makeComponent(Checkbox::class, $customField, false);

        return $checkbox;
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): IconEntry
    {
        /**@var $iconEntry IconEntry */
        $iconEntry = $this->makeComponent(IconEntry::class, $customFieldAnswer, true);

        return $iconEntry
            ->state(is_null($this->getAnswer($customFieldAnswer)) ? false : $this->getAnswer($customFieldAnswer))
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }
}
