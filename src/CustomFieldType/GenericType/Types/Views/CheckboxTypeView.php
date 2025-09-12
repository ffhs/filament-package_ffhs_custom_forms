<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Support\Components\Component;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;

class CheckboxTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Checkbox
    {
        /**@var $checkbox Checkbox */
        $checkbox = $this->makeComponent(Checkbox::class, $record, false);

        return $checkbox;
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): IconEntry {
        /**@var $iconEntry IconEntry */
        $iconEntry = $this->makeComponent(IconEntry::class, $record, true);

        return $iconEntry
            ->state(is_null($this->getAnswer($record)) ? false : $this->getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }
}
