<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Support\Components\Component;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Section as FormsSection;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section as InfolistsSection;

class SectionTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        $section = FormsSection::make($this->getLabelName($record));
        $section = $this->modifyFormComponent($section, $record);

        /**@var $section FormsSection */
        return $section
            ->aside($this->getOptionParameter($record, 'aside'))
            ->schema($parameter['child_render']());
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        $schema = $parameter['child_render']();

        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return Group::make($schema)
                ->columnStart(1)
                ->columnSpanFull();
        }

        if ($this->getOptionParameter($record, 'show_as_fieldset')) {
            return Fieldset::make($this->getLabelName($record))
                ->schema($schema)
                ->columnStart(1)
                ->columnSpanFull();
        }

        return InfolistsSection::make($this->getLabelName($record))
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }
}
