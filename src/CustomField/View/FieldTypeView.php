<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\View;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

interface FieldTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component;
    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component;




}
