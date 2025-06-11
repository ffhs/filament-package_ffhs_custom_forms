<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;

class SectionTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        $section = Section::make($this->getLabelName($record));
        $section = $this->modifyFormComponent($section, $record);
        /**@var $section Section */
        return $section
            ->aside($this->getOptionParameter($record, "aside"))
            ->schema($parameter["child_render"]());
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $schema = $parameter["child_render"]();

        if (!$this->getOptionParameter($record, "show_in_view")) {
            return Group::make($schema)->columnStart(1)->columnSpanFull();
        }

        if ($this->getOptionParameter($record, "show_as_fieldset")) {
            return Fieldset::make($this->getLabelName($record))
                ->schema($schema)
                ->columnStart(1)
                ->columnSpanFull();
        }

        return \Filament\Infolists\Components\Section::make($this->getLabelName($record))
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }
}
