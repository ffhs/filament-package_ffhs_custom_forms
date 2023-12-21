<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\TextInput;

class EmailTypeView extends TextTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): TextInput {
        return parent::getFormComponent($type, $record, $parameter)
            ->email();
    }


}
