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

class GroupTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        return $this
            ->modifyFormComponent(Group::make(), $record)
            ->schema($parameter['child_render']());
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        $schema = $parameter['child_render']();

        if ($this->getOptionParameter($record, 'show_in_view')) {
            $fieldset = Fieldset::make($this->getLabelName($record));

            return $this
                ->modifyInfolistComponent($fieldset, $record, ['show_in_view'])
                ->columnStart(1)
                ->schema($schema)
                ->columnSpanFull();
        }

        $group = $this->modifyInfolistComponent(Group::make(), $record, ['show_in_view']);

        return $group
            ->columnStart(1)
            ->schema($schema)
            ->columnSpanFull();
    }
}
