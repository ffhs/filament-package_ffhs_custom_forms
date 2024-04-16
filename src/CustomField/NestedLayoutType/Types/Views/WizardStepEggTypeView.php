<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Wizard;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Tabs\Tab;

class WizardStepEggTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): \Filament\Forms\Components\Component {

        return Wizard\Step::make(FormMapper::getLabelName($record))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->icon(FormMapper::getOptionParameter($record,"icon"))
            ->description(FormMapper::getToolTips($record))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {
        return Fieldset::make(FormMapper::getLabelName($record))
            ->schema($parameter["rendered"])
            ->columnStart(1)
            ->columnSpanFull();
    }

}
