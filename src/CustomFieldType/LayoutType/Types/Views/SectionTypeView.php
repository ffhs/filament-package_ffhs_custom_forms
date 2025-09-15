<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Components\Component;

class SectionTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        EmbedCustomField $customField,
        array $parameter = []
    ): Component {
        $section = Section::make($this->getLabelName($customField));
        return $this
            ->modifyComponent($section, $customField, false)
            ->aside($this->getOptionParameter($customField, 'aside'))
            ->schema($parameter['child_render']())
            ->label('');
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        $schema = $parameter['child_render']();

        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return Group::make($schema)
                ->columnSpanFull()
                ->columnStart(1);
        }

        if ($this->getOptionParameter($record, 'show_as_fieldset')) {
            return Fieldset::make($this->getLabelName($record))
                ->schema($schema)
                ->columnStart(1)
                ->columnSpanFull();
        }

        return Section::make($this->getLabelName($record))
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }
}
