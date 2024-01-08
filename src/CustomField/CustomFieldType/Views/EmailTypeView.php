<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\TextInput;

class EmailTypeView extends TextTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record, array $parameter = []): TextInput {
        return parent::getFormComponent($type, $record, $parameter)
            ->email();
    }


}
