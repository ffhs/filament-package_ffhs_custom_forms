<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;

class FieldsetTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $fieldSet = $this->modifyComponent(Fieldset::make($this->getLabelName($customField)), $customField, false);

        return $fieldSet
            ->columnSpan($this->getOptionParameter($customField, 'column_span'))
            ->columnStart($this->getOptionParameter($customField, 'new_line'))
            ->schema($parameter['child_render']());
    }


    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $schema = $parameter['child_render']();

        if (!$this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            return $this
                ->modifyComponent(Group::make($schema), $customFieldAnswer, true)
                ->columnStart(1)
                ->columnSpanFull();
        }

        $fieldSet = Fieldset::make($this->getLabelName($customFieldAnswer));

        return $this
            ->modifyComponent($fieldSet, $customFieldAnswer, true)
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }
}
