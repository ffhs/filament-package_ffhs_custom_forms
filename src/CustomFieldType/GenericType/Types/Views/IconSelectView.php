<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Filament\Infolists\Components\IconEntry;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconSelectView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): FormsComponent {
        return $this->makeComponent(IconPicker::class, $record);
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistsComponent {
        return $this
            ->makeComponent(IconEntry::class, $record)
            ->icon($this->getAnswer($record));
    }
}
