<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Fieldset as FormsFieldset;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Filament\Infolists\Components\Fieldset as InfolistsFieldset;
use Filament\Infolists\Components\Group;

class FieldsetTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): FormsComponent {
        /**@var $fieldSet FormsFieldset */
        $fieldSet = $this->modifyFormComponent(FormsFieldset::make($this->getLabelName($record)), $record);

        return $fieldSet
            ->columnSpan($this->getOptionParameter($record, 'column_span'))
            ->columnStart($this->getOptionParameter($record, 'new_line'))
            ->schema($parameter['child_render']());
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistsComponent {
        $schema = $parameter['child_render']();

        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return $this
                ->modifyInfolistComponent(Group::make($schema), $record)
                ->columnStart(1)
                ->columnSpanFull();
        }

        $fieldSet = InfolistsFieldset::make($this->getLabelName($record));

        return $this
            ->modifyInfolistComponent($fieldSet, $record)
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }
}
