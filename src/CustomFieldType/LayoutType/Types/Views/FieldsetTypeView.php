<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;

class FieldsetTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        $fieldSet = $this->modifyFormComponent(Fieldset::make($this->getLabelName($record)), $record, false);

        return $fieldSet
            ->columnSpan($this->getOptionParameter($record, 'column_span'))
            ->columnStart($this->getOptionParameter($record, 'new_line'))
            ->schema($parameter['child_render']());
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        $schema = $parameter['child_render']();

        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return $this
                ->modifyInfolistComponent(Group::make($schema), $record)
                ->columnStart(1)
                ->columnSpanFull();
        }

        $fieldSet = Fieldset::make($this->getLabelName($record));

        return $this
            ->modifyInfolistComponent($fieldSet, $record)
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }
}
