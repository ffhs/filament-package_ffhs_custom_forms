<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\CheckboxList;
use Filament\Support\Components\Component;

class CheckboxListTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView;
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        return $this->makeComponent(CheckboxList::class, $record, false)
            ->options($this->getAvailableCustomOptions($record))
            ->maxItems($this->getOptionParameter($record, 'max_items'))
            ->minItems($this->getOptionParameter($record, 'min_items'));
    }
}
