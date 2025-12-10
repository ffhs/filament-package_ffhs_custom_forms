<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;

//ToDo replace range picker
class DateRangeTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;


    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        return $this->makeComponent(Hidden::class, $customField, false); //ToDo Replace
//        /**@var Flatpickr $flatpickr */
//        $flatpickr = $this->makeComponent(Flatpickr::class, $customField, false);
//
//        return $flatpickr->rangePicker();
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        return $this->makeComponent(TextEntry::class, $customFieldAnswer, true);
    }
}
