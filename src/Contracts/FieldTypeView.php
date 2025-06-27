<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistsComponent;

interface FieldTypeView
{
    public static function make(): static;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): FormsComponent;

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistsComponent;
}
