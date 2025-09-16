<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;

class GroupTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        return $this
            ->modifyComponent(Group::make(), $customField, false)
            ->schema($parameter['child_render']());
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $schema = $parameter['child_render']();

        if ($this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            $fieldset = Fieldset::make($this->getLabelName($customFieldAnswer));

            return $this
                ->modifyComponent($fieldset, $customFieldAnswer, true, ['show_in_view'])
                ->columnStart(1)
                ->schema($schema)
                ->columnSpanFull();
        }

        $group = $this->modifyComponent(Group::make(), $customFieldAnswer, true, ['show_in_view']);

        return $group
            ->columnStart(1)
            ->schema($schema)
            ->columnSpanFull();
    }
}
