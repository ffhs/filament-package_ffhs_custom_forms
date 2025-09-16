<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;

class TextAreaTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Textarea
    {
        return $this
            ->makeComponent(Textarea::class, $record, false)
            ->autosize($this->getOptionParameter($record, 'auto_size'));
    }

    public function getEntryComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        return $this->makeComponent(TextEntry::class, $record, true);
    }
}
