<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;

interface FieldTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record, array $parameter = []): \Filament\Forms\Components\Component;
    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component;

}
