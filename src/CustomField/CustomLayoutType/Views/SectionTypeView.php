<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\IconEntry;

class SectionTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Section {
        return Section::make($type->getOptionParameter($record,"show_title")? $type::getLabelName($record):"")
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->columns($type->getOptionParameter($record,"columns"))
            ->aside($type->getOptionParameter($record,"aside"))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Section {
        return \Filament\Infolists\Components\Section::make($type::class::getLabelName($record)) ->schema($parameter["rendered"]);
    }

}
