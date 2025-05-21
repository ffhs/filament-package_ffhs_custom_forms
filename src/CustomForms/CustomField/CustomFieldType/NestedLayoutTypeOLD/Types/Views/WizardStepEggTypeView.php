<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldType\NestedLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Wizard;
use Filament\Infolists\Components\Fieldset;

class WizardStepEggTypeView implements FieldTypeView
{

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        return Wizard\Step::make(FieldMapper::getLabelName($record))
            ->description(FieldMapper::getOptionParameter($record, "helper_text"))
            ->schema($parameter["rendered"]);
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        return Fieldset::make(FieldMapper::getLabelName($record))
            ->schema($parameter["rendered"])
            ->columnStart(1)
            ->columnSpanFull();
    }

}
