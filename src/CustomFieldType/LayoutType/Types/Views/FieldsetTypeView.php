<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Fieldset;
use Filament\Infolists\Components\Group;

class FieldsetTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        /**@var $fieldSet Fieldset */
        $fieldSet = $this->modifyFormComponent(Fieldset::make($this->getLabelName($record)), $record);
        return $fieldSet
            ->columnSpan($this->getOptionParameter($record, "column_span"))
            ->columnStart($this->getOptionParameter($record, "new_line"))
            ->schema($parameter['child_render']());
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $schema = $parameter["child_render"]();

        if (!$this->getOptionParameter($record, "show_in_view")) {
            return $this->modifyInfolistComponent(Group::make($schema), $record)
                ->columnStart(1)
                ->columnSpanFull();
        }

        $fieldSet = \Filament\Infolists\Components\Fieldset::make($this->getLabelName($record));
        return $this->modifyInfolistComponent($fieldSet, $record)
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }
}
