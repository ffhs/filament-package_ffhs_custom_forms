<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Components\Component;

class SectionTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $section = Section::make($this->getLabelName($customField));
        return $this
            ->modifyComponent($section, $customField, false)
            ->aside($this->getOptionParameter($customField, 'aside'))
            ->schema($parameter['child_render']())
            ->hiddenLabel();
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $schema = $parameter['child_render']();

        if (!$this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            return Group::make($schema)
                ->columnSpanFull()
                ->columnStart(1);
        }

        if ($this->getOptionParameter($customFieldAnswer, 'show_as_fieldset')) {
            return Fieldset::make($this->getLabelName($customFieldAnswer))
                ->schema($schema)
                ->columnStart(1)
                ->hiddenLabel()
                ->columnSpanFull();
        }

        return Section::make($this->getLabelName($customFieldAnswer))
            ->schema($schema)
            ->columnStart(1)
            ->hiddenLabel()
            ->columnSpanFull();
    }
}
