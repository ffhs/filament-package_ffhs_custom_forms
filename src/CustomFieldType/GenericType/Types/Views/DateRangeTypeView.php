<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Coolsam\Flatpickr\Forms\Components\Flatpickr;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;

//ToDo replace range picker
class DateRangeTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Component
    {
        /**@var Flatpickr $flatpickr */
        $flatpickr = $this->makeComponent(Flatpickr::class, $record, false); //ToDoReplace

        return $flatpickr->rangePicker();
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        return $this->makeComponent(TextEntry::class, $record, true);
    }
}
