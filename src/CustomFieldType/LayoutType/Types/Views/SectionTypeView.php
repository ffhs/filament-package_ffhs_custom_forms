<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
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
        CustomField $record,
        array $parameter = []
    ): Component {
        $section = Section::make($this->getLabelName($record));
        return $this
            ->modifyFormComponent($section, $record)
            ->aside($this->getOptionParameter($record, 'aside'))
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
