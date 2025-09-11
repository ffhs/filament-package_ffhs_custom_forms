<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Support\Components\Component;

interface FieldTypeView
{
    public static function make(): static;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component;

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component;
}
