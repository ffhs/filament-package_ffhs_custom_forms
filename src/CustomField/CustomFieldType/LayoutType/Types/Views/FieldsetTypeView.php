<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Fieldset;
use Filament\Infolists\Components\Group;

class FieldsetTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        /**@var $fieldSet Fieldset */
        $fieldSet = static::modifyFormComponent(Fieldset::make(FieldMapper::getLabelName($record)), $record);
        return $fieldSet
            ->columnSpan(FieldMapper::getOptionParameter($record, "column_span"))
            ->columnStart(FieldMapper::getOptionParameter($record, "new_line_option"))
            ->schema($parameter["renderer"]());
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $schema = $parameter["renderer"]();

        if (!FieldMapper::getOptionParameter($record, "show_in_view")) {
            return static::modifyInfolistComponent(Group::make($schema), $record)
                ->columnStart(1)
                ->columnSpanFull();
        } else {
            /**@var $fieldSet \Filament\Infolists\Components\Fieldset */
            $fieldSet = \Filament\Infolists\Components\Fieldset::make(FieldMapper::getLabelName($record));
            $fieldSet = static::modifyInfolistComponent($fieldSet, $record);
            return $fieldSet
                ->schema($schema)
                ->columnStart(1)
                ->columnSpanFull();
        }
    }

}
