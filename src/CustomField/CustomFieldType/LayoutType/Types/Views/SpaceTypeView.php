<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;

class SpaceTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        $spaces = [];

        for ($count = 0; $count < FieldMapper::getOptionParameter($record, "amount"); $count += 1) {
            $spaces[] = Placeholder::make(FieldMapper::getIdentifyKey($record) . "-" . $count)
                ->content("")
                ->label("")
                ->columnSpanFull();
        }

        return static::modifyFormComponent(Group::make($spaces), $record)
            ->columns(1)
            ->columnSpanFull();
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        if (!FieldMapper::getOptionParameter($record, "show_in_view")) {
            return \Filament\Infolists\Components\Group::make()->hidden();
        }

        $spaces = [];

        for ($count = 0; $count < FieldMapper::getOptionParameter($record, "amount"); $count += 1) {
            $spaces[] = TextEntry::make(FieldMapper::getIdentifyKey($record) . "-" . $count)
                ->state(" ")
                ->label("");
        }
        return static::modifyInfolistComponent(\Filament\Infolists\Components\Group::make($spaces), $record)
            ->columns(1)
            ->columnSpanFull();
    }

}
