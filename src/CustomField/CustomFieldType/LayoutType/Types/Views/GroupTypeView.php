<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\Fieldset;

class GroupTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        return static::modifyFormComponent(Group::make(), $record)
            ->schema($parameter["renderer"]());
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $schema = $parameter["renderer"]();

        if (FieldMapper::getOptionParameter($record, "show_in_view")) {
            $fieldset = Fieldset::make(FieldMapper::getLabelName($record));
            return static::modifyInfolistComponent($fieldset, $record, ['show_in_view'])
                ->columnStart(1)
                ->schema($schema)
                ->columnSpanFull();
        }
        $group = static::modifyInfolistComponent(\Filament\Infolists\Components\Group::make(), $record, ['show_in_view']
        );
        return $group
            ->columnStart(1)
            ->schema($schema)
            ->columnSpanFull();
    }

}
