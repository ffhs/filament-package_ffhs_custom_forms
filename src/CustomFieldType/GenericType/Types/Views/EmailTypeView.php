<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Support\Components\Component;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class EmailTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        return $this
            ->makeComponent(TextInput::class, $record, false)
            ->email();
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        return $this->makeComponent(TextEntry::class, $record, true);
    }
}
