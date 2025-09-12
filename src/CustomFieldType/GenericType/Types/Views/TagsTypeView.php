<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\TagsInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;

class TagsTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        return $this->makeComponent(TagsInput::class, $record, false);
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        $answerer = $this->getAnswer($record);
        $answerer = empty($answerer) ? '' : $answerer;

        return $this
            ->makeComponent(TextEntry::class, $record, true)
            ->state($answerer)
            ->badge();
    }
}
