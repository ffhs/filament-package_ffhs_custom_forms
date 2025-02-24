<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;

class SectionTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        $section = Section::make(FieldMapper::getLabelName($record));
        /**@var $section Section */
        $section = static::modifyFormComponent($section, $record);
        return $section
            ->aside(FieldMapper::getOptionParameter($record, "aside"))
            ->schema($parameter["renderer"]());
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $schema = $parameter["renderer"]();

        if (!FieldMapper::getOptionParameter($record, "show_in_view")) {
            return Group::make($schema)->columnStart(1)->columnSpanFull();
        }


        if (FieldMapper::getOptionParameter($record, "show_as_fieldset")) {
            return Fieldset::make(FieldMapper::getLabelName($record))
                ->schema($schema)
                ->columnStart(1)
                ->columnSpanFull();
        }

        return \Filament\Infolists\Components\Section::make(FieldMapper::getLabelName($record))
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }

}
