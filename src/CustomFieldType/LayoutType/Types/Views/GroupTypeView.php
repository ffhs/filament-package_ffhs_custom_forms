<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group as InfolistGroup;

class GroupTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        return $this->modifyFormComponent(Group::make(), $record)
            ->schema($parameter["child_render"]());
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $schema = $parameter["child_render"]();

        if ($this->getOptionParameter($record, "show_in_view")) {
            $fieldset = Fieldset::make($this->getLabelName($record));
            return $this->modifyInfolistComponent($fieldset, $record, ['show_in_view'])
                ->columnStart(1)
                ->schema($schema)
                ->columnSpanFull();
        }
        $group = $this->modifyInfolistComponent(InfolistGroup::make(), $record, ['show_in_view']);
        return $group
            ->columnStart(1)
            ->schema($schema)
            ->columnSpanFull();
    }
}
