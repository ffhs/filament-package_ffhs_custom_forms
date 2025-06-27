<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Coolsam\Flatpickr\Forms\Components\Flatpickr;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Filament\Infolists\Components\TextEntry;

class DateRangeTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): FormsComponent
    {
        /**@var Flatpickr $flatpickr */
        $flatpickr = $this->makeComponent(Flatpickr::class, $record);

        return $flatpickr->rangePicker();
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistsComponent {
        return $this->makeComponent(TextEntry::class, $record);
    }
}
